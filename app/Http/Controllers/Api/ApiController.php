<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\BlogPost;
use App\Models\BlogPostCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ProjectCategory;
use App\Models\Slider;
use App\Models\News;
use App\Models\Media;
use App\Models\Reference;
use App\Models\Brand;
use App\Models\WelcomeMessage;
use App\Models\Client;
use App\Models\PageView;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected function getClient(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }
        return Client::where('api_token', $token)->first();
    }

    // 0. Company Info
    public function languages(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $languages = $client->languages()->where('is_active', true)->get(['name', 'code', 'is_default', 'icon']);

        return response()->json([
            'status' => 'success',
            'data' => $languages
        ]);
    }

    public function companyInfo(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'company_name'       => $client->company_name,
                'address'            => $client->address,
                'additional_address' => $client->additional_address,
                'phone'              => $client->phone,
                'fax'                => $client->fax,
                'additional_contact' => $client->additional_contact,
                'instagram'          => $client->instagram,
                'facebook'           => $client->facebook,
                'whatsapp'           => $client->whatsapp,
                'coordinates'        => $client->coordinates,
            ]
        ]);
    }

    // 0. Track Visit
    public function trackVisit(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $url = $request->input('url');
        $today = now()->toDateString();

        // Aynı IP ile aynı gün giriş yapılmış mı kontrol et (Tekil Ziyaretçi Mantığı)
        $exists = PageView::where('client_id', $client->id)
            ->where('ip_address', $ip)
            ->where('visited_at', $today)
            ->exists();

        if (!$exists) {
            PageView::create([
                'client_id' => $client->id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'url' => $url,
                'visited_at' => $today,
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    // 1. Welcome Message
    public function welcomeMessage(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('welcome_message')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $welcomeMessage = WelcomeMessage::where('client_id', $client->id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $welcomeMessage ? [
                'title' => $welcomeMessage->title,
                'description' => $welcomeMessage->description,
                'image_url' => $welcomeMessage->image ? asset('storage/' . $welcomeMessage->image) : null,
                'status' => $welcomeMessage->status,
                'created_at' => $welcomeMessage->created_at->toIso8601String(),
            ] : null
        ]);
    }

    // 2. Sliders
    public function sliders(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('slider_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $sliders = Slider::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($slider) {
                return [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'slug' => $slider->slug,
                    'description' => $slider->description,
                    'image_url' => $slider->image ? asset('storage/' . $slider->image) : null,
                    'sort_order' => $slider->sort_order,
                    'created_at' => $slider->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $sliders
        ]);
    }

    // 3. News (Haberler)
    public function newsList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('news_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $news = News::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'description' => $item->description,
                    'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                    'sort_order' => $item->sort_order,
                    'created_at' => $item->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $news
        ]);
    }

    public function newsDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('news_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = News::with('gallery')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'sort_order' => $item->sort_order,
                'created_at' => $item->created_at->toIso8601String(),
            ]
        ]);
    }

    // 4. Media (Medya)
    public function mediaList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('media_gallery')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $media = Media::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'description' => $item->description,
                    'sort_order' => $item->sort_order,
                    'created_at' => $item->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $media
        ]);
    }

    public function mediaDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('media_gallery')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = Media::with('gallery')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media gallery not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'sort_order' => $item->sort_order,
                'created_at' => $item->created_at->toIso8601String(),
            ]
        ]);
    }

    // 5. References (Referanslar)
    public function referencesList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('reference_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $references = Reference::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'description' => $item->description,
                    'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                    'sort_order' => $item->sort_order,
                    'created_at' => $item->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $references
        ]);
    }

    public function referenceDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('reference_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = Reference::query()
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reference not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'sort_order' => $item->sort_order,
                'created_at' => $item->created_at->toIso8601String(),
            ]
        ]);
    }

    // 6. Project Categories (Proje Kategorileri)
    public function projectCategories(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('project_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $categories = ProjectCategory::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image_url' => $category->image ? asset('storage/' . $category->image) : null,
                    'sort_order' => $category->sort_order,
                    'created_at' => $category->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    // 7. Projects (Projeler)
    public function projectsList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('project_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $query = Project::with('category')
            ->where('client_id', $client->id)
            ->where('status', 'active');

        if ($request->has('category_id')) {
            $query->where('project_category_id', $request->input('category_id'));
        }

        $projects = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'description' => $project->description,
                    'image_url' => $project->image ? asset('storage/' . $project->image) : null,
                    'category' => $project->category ? [
                        'id' => $project->category->id,
                        'title' => $project->category->title,
                        'slug' => $project->category->slug,
                    ] : null,
                    'sort_order' => $project->sort_order,
                    'created_at' => $project->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }

    public function projectDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('project_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $project = Project::with(['category', 'gallery'])
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'image_url' => $project->image ? asset('storage/' . $project->image) : null,
                'category' => $project->category ? [
                    'id' => $project->category->id,
                    'title' => $project->category->title,
                    'slug' => $project->category->slug,
                ] : null,
                'gallery' => $project->gallery->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'image_url' => asset('storage/' . $item->image),
                        'sort_order' => $item->sort_order,
                    ];
                }),
                'sort_order' => $project->sort_order,
                'created_at' => $project->created_at->toIso8601String(),
            ]
        ]);
    }

    // 8. Product Categories (Ürün Kategorileri)
    public function productCategories(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('product_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $categories = ProductCategory::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image_url' => $category->image ? asset('storage/' . $category->image) : null,
                    'sort_order' => $category->sort_order,
                    'created_at' => $category->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    // 9. Products (Ürünler)
    public function productsList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('product_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $query = Product::with('category')
            ->where('client_id', $client->id)
            ->where('status', 'active');

        if ($request->has('category_id')) {
            $query->where('product_category_id', $request->input('category_id'));
        }

        $products = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'title' => $product->category->title,
                        'slug' => $product->category->slug,
                    ] : null,
                    'sort_order' => $product->sort_order,
                    'created_at' => $product->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function productDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid Bearer token.'], 401);
        }

        if (!$client->hasModule('product_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $product = Product::with(['category', 'gallery'])
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'description' => $product->description,
                'image_url' => $product->image ? asset('storage/' . $product->image) : null,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'title' => $product->category->title,
                    'slug' => $product->category->slug,
                ] : null,
                'gallery' => $product->gallery->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'image_url' => asset('storage/' . $item->image),
                        'sort_order' => $item->sort_order,
                    ];
                }),
                'sort_order' => $product->sort_order,
                'created_at' => $product->created_at->toIso8601String(),
            ]
        ]);
    }

    // --- Gallery Only Endpoints ---

    // News Gallery
    public function newsGallery(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid Bearer token.'], 401);
        }

        if (!$client->hasModule('news_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = News::with('gallery')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'News not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item->gallery->map(function ($gal) {
                return [
                    'id' => $gal->id,
                    'image_url' => asset('storage/' . $gal->image),
                    'sort_order' => $gal->sort_order,
                ];
            })
        ]);
    }

    // Media Gallery
    public function mediaGallery(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid Bearer token.'], 401);
        }

        if (!$client->hasModule('media_gallery')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = Media::with('gallery')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Media gallery not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item->gallery->map(function ($gal) {
                return [
                    'id' => $gal->id,
                    'image_url' => asset('storage/' . $gal->image),
                    'sort_order' => $gal->sort_order,
                ];
            })
        ]);
    }

    

    // Project Gallery
    public function projectGallery(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid Bearer token.'], 401);
        }

        if (!$client->hasModule('project_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = Project::with('gallery')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Project not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item->gallery->map(function ($gal) {
                return [
                    'id' => $gal->id,
                    'image_url' => asset('storage/' . $gal->image),
                    'sort_order' => $gal->sort_order,
                ];
            })
        ]);
    }

    // Product Gallery
    public function productGallery(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid Bearer token.'], 401);
        }

        if (!$client->hasModule('product_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = Product::with('gallery')
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item->gallery->map(function ($gal) {
                return [
                    'id' => $gal->id,
                    'image_url' => asset('storage/' . $gal->image),
                    'sort_order' => $gal->sort_order,
                ];
            })
        ]);
    }

    // Brands (Markalar)
    public function brandsList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('brand_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $brands = Brand::where('client_id', $client->id)
            ->where('status', 'active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'description' => $item->description,
                    'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                    'website_url' => $item->website_url,
                    'created_at' => $item->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $brands
        ]);
    }

    public function brandDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. Please provide a valid api_token.'], 401);
        }

        if (!$client->hasModule('brand_management')) {
            return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403);
        }

        $item = Brand::where('client_id', $client->id)
            ->where('status', 'active')
            ->find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'website_url' => $item->website_url,
                'created_at' => $item->created_at->toIso8601String(),
            ]
        ]);
    }

    // --- Blog Posts ---
    public function blogPostCategories(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('blog_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $categories = BlogPostCategory::where('client_id', $client->id)->where('status', 'active')->orderBy('sort_order', 'asc')->get(['id', 'title', 'slug', 'description', 'image']);
        $categories->transform(function($cat) {
            $cat->image_url = $cat->image ? asset('storage/' . $cat->image) : null;
            unset($cat->image);
            return $cat;
        });

        return response()->json(['status' => 'success', 'data' => $categories]);
    }

    public function blogPostsList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('blog_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $posts = BlogPost::with('category')->where('client_id', $client->id)->where('status', 'active')->orderBy('sort_order', 'asc')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'category' => $item->category ? ['id' => $item->category->id, 'title' => $item->category->title] : null,
                'created_at' => $item->created_at->toIso8601String(),
            ];
        });

        return response()->json(['status' => 'success', 'data' => $posts]);
    }

    public function blogPostDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('blog_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $item = BlogPost::with(['category', 'gallery'])->where('client_id', $client->id)->where('status', 'active')->find($id);
        if (!$item) { return response()->json(['status' => 'error', 'message' => 'Blog post not found.'], 404); }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'content' => $item->content,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'category' => $item->category ? ['id' => $item->category->id, 'title' => $item->category->title] : null,
                'gallery' => $item->gallery->map(function ($gal) {
                    return ['id' => $gal->id, 'image_url' => asset('storage/' . $gal->image), 'sort_order' => $gal->sort_order];
                }),
                'created_at' => $item->created_at->toIso8601String(),
            ]
        ]);
    }

    public function blogPostGallery(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('blog_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $item = BlogPost::with('gallery')->where('client_id', $client->id)->where('status', 'active')->find($id);
        if (!$item) { return response()->json(['status' => 'error', 'message' => 'Blog post not found.'], 404); }

        return response()->json([
            'status' => 'success',
            'data' => $item->gallery->map(function ($gal) {
                return ['id' => $gal->id, 'image_url' => asset('storage/' . $gal->image), 'sort_order' => $gal->sort_order];
            })
        ]);
    }

    // --- Services ---
    public function serviceCategories(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('service_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $categories = ServiceCategory::where('client_id', $client->id)->where('status', 'active')->orderBy('sort_order', 'asc')->get(['id', 'title', 'slug', 'description', 'image']);
        $categories->transform(function($cat) {
            $cat->image_url = $cat->image ? asset('storage/' . $cat->image) : null;
            unset($cat->image);
            return $cat;
        });

        return response()->json(['status' => 'success', 'data' => $categories]);
    }

    public function servicesList(Request $request)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('service_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $services = Service::with('category')->where('client_id', $client->id)->where('status', 'active')->orderBy('sort_order', 'asc')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'category' => $item->category ? ['id' => $item->category->id, 'title' => $item->category->title] : null,
                'created_at' => $item->created_at->toIso8601String(),
            ];
        });

        return response()->json(['status' => 'success', 'data' => $services]);
    }

    public function serviceDetail(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('service_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $item = Service::with(['category', 'gallery'])->where('client_id', $client->id)->where('status', 'active')->find($id);
        if (!$item) { return response()->json(['status' => 'error', 'message' => 'Service not found.'], 404); }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'description' => $item->description,
                'content' => $item->content,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                'category' => $item->category ? ['id' => $item->category->id, 'title' => $item->category->title] : null,
                'gallery' => $item->gallery->map(function ($gal) {
                    return ['id' => $gal->id, 'image_url' => asset('storage/' . $gal->image), 'sort_order' => $gal->sort_order];
                }),
                'created_at' => $item->created_at->toIso8601String(),
            ]
        ]);
    }

    public function serviceGallery(Request $request, $id)
    {
        $client = $this->getClient($request);
        if (!$client) { return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401); }
        if (!$client->hasModule('service_management')) { return response()->json(['status' => 'error', 'message' => 'Module not enabled for this client'], 403); }

        $item = Service::with('gallery')->where('client_id', $client->id)->where('status', 'active')->find($id);
        if (!$item) { return response()->json(['status' => 'error', 'message' => 'Service not found.'], 404); }

        return response()->json([
            'status' => 'success',
            'data' => $item->gallery->map(function ($gal) {
                return ['id' => $gal->id, 'image_url' => asset('storage/' . $gal->image), 'sort_order' => $gal->sort_order];
            })
        ]);
    }
}
