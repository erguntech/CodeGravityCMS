<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:products.view', only: ['index', 'show']),
            new Middleware('can:products.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
            $data = Product::with('category')->select(['id', 'product_category_id', 'title', 'status', 'image', 'created_at', 'sort_order']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('title', function ($row) use ($clientDefaultLang) {
                    $titleData = $row->getTranslations('title');
                    return $titleData[$clientDefaultLang] ?? reset($titleData);
                })
                ->editColumn('id', function ($row) {
                    return '<span class="badge badge-light-primary fw-bold">#' . $row->id . '</span>';
                })
                ->addColumn('category', function ($row) {
                    return $row->category ? $row->category->title : '-';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d.m.Y H:i');
                })
                ->editColumn('status', function ($row) {
                    $status = $row->status ?? 'active';
                    $badgeClass = $status === 'active' ? 'success' : 'danger';
                    $label = $status === 'active' ? __('Aktif') : __('Pasif');
                    return '<span class="badge badge-light-' . $badgeClass . ' fw-bold">' . $label . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $canManage = auth()->user()->can('products.manage');
                    
                    $viewBtn = '';
                    if ($row->image) {
                        $viewBtn = '<a data-fslightbox="products-gallery" data-type="image" href="' . asset('storage/' . $row->image) . '" class="btn btn-icon btn-light-primary btn-sm me-1" title="' . __('Resmi Görüntüle') . '">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                   </a>';
                    }

                    if ($canManage) {
                        $routePrefix = 'client.';
                        $editUrl = route($routePrefix . 'products.edit', $row->id);
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1';
 
                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm delete-record" data-id="' . $row->id . '" title="' . __('Sil') . '">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                               </a>';
                    } else {
                        $editUrl = 'javascript:void(0)';
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1 unauthorized-action-btn';
 
                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm unauthorized-action-btn" title="' . __('Sil') . '">
                                <i class="ki-duotone ki-trash fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                </i>
                           </a>';
                    }
 
                    $galleryBtn = '';
                    if ($canManage) {
                        $galleryUrl = route('client.products.gallery', $row->id);
                        $galleryBtn = '<a href="' . $galleryUrl . '" class="btn btn-icon btn-light-info btn-sm me-1" title="' . __('Galeri') . '">
                                            <i class="ki-duotone ki-picture fs-2">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                       </a>';
                    }

                    $btn = '<div class="d-flex justify-content-end flex-shrink-0">
                                ' . $viewBtn . '
                                ' . $galleryBtn . '
                                <a href="' . $editUrl . '" class="' . $editClass . '" title="' . __('Düzenle') . '">
                                    <i class="ki-duotone ki-pencil fs-2">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                </a>
                                ' . $deleteBtn . '
                            </div>';
                    return $btn;
                })
                ->rawColumns(['id', 'status', 'action'])
                ->make(true);
        }
 
        return view('pages.backend.products.index');
    }

    public function create()
    {
        $categories = ProductCategory::all();
        return view('pages.backend.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Ürün Başlığı'),
            'title.' . $clientDefaultLang => __('Ürün Başlığı'),
            'product_category_id' => __('Kategori'),
            'description' => __('Ürün Açıklaması'),
            'price' => __('Ürün Fiyatı'),
            'discounted_price' => __('İndirimli Fiyatı'),
            'status' => __('Durumu'),
            'image' => __('Ürün Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', 'image']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('product');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $data['sort_order'] = Product::max('sort_order') + 1;
        Product::create($data);

        return redirect()->route('client.products.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::all();
        return view('pages.backend.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'product_category_id' => 'required|exists:product_categories,id',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Ürün Başlığı'),
            'title.' . $clientDefaultLang => __('Ürün Başlığı'),
            'product_category_id' => __('Kategori'),
            'description' => __('Ürün Açıklaması'),
            'price' => __('Ürün Fiyatı'),
            'discounted_price' => __('İndirimli Fiyatı'),
            'status' => __('Durumu'),
            'image' => __('Ürün Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', '_method', 'image']);
        
        if ($request->title !== $product->title) {
            $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);
        }

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('product');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $product->update($data);

        return redirect()->route('client.products.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return response()->json(['status' => 'success', 'message' => __('Ürün başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function gallery(Product $product)
    {
        $gallery = $product->gallery;
        return view('pages.backend.products.gallery', compact('product', 'gallery'));
    }

    public function storeGallery(Request $request, Product $product)
    {
        if (!$request->hasFile('images')) {
            return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
        }

        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'images' => 'array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ], [], [
            'images' => __('Resimler'),
            'images.*' => __('Resim')
        ]);

        $lastSortOrder = $product->gallery()->max('sort_order') ?? 0;
        foreach ($request->file('images') as $file) {
            $lastSortOrder++;
            $path = $file->store('products/gallery', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('product_gallery');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($path), $size['width'], $size['height']);
                }
            }

            ProductGallery::create([
                'product_id' => $product->id,
                'image' => $path,
                'sort_order' => $lastSortOrder
            ]);
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroyGallery(Product $product, ProductGallery $image)
    {
        try {
            Storage::disk('public')->delete($image->image);
            $image->delete();
            return response()->json(['status' => 'success', 'message' => __('Resim galeriden başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function reorderGallery(Request $request, Product $product)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer'
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            ProductGallery::where('id', $id)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => __('Sıralama güncellendi.')]);
    }

    public function reorder()
    {
        $products = Product::orderBy('sort_order', 'asc')->get();
        return view('pages.backend.products.reorder', compact('products'));
    }

    public function updateOrder(Request $request)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer'
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            Product::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => __('Sıralama güncellendi.')]);
    }
}
