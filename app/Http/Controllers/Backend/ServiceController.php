<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceGallery;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:services.view', only: ['index', 'show']),
            new Middleware('can:services.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
            $data = Service::with('category')->select(['id', 'service_category_id', 'title', 'status', 'image', 'created_at', 'sort_order']);
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
                    $canManage = auth()->user()->can('services.manage');
                    
                    $viewBtn = '';
                    if ($row->image) {
                        $viewBtn = '<a data-fslightbox="services-gallery" data-type="image" href="' . asset('storage/' . $row->image) . '" class="btn btn-icon btn-light-primary btn-sm me-1" title="' . __('Resmi Görüntüle') . '">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                   </a>';
                    }

                    if ($canManage) {
                        $routePrefix = 'client.';
                        $editUrl = route($routePrefix . 'services.edit', $row->id);
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
                        $galleryUrl = route('client.services.gallery', $row->id);
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
 
        return view('pages.backend.services.index');
    }

    public function create()
    {
        $categories = ServiceCategory::all();
        return view('pages.backend.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Hizmet Başlığı'),
            'title.' . $clientDefaultLang => __('Hizmet Başlığı'),
            'service_category_id' => __('Kategori'),
            'description' => __('Hizmet Açıklaması'),
            'status' => __('Durumu'),
            'image' => __('Hizmet Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', 'image']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('service');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $data['sort_order'] = Service::max('sort_order') + 1;
        Service::create($data);

        return redirect()->route('client.services.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::all();
        return view('pages.backend.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'service_category_id' => 'required|exists:service_categories,id',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Hizmet Başlığı'),
            'title.' . $clientDefaultLang => __('Hizmet Başlığı'),
            'service_category_id' => __('Kategori'),
            'description' => __('Hizmet Açıklaması'),
            'status' => __('Durumu'),
            'image' => __('Hizmet Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', '_method', 'image']);
        
        if ($request->title !== $service->title) {
            $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);
        }

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('service');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $service->update($data);

        return redirect()->route('client.services.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(Service $service)
    {
        try {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $service->delete();
            return response()->json(['status' => 'success', 'message' => __('Hizmet başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function gallery(Service $service)
    {
        $gallery = $service->gallery;
        return view('pages.backend.services.gallery', compact('service', 'gallery'));
    }

    public function storeGallery(Request $request, Service $service)
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

        $lastSortOrder = $service->gallery()->max('sort_order') ?? 0;
        foreach ($request->file('images') as $file) {
            $lastSortOrder++;
            $path = $file->store('services/gallery', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('service_gallery');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($path), $size['width'], $size['height']);
                }
            }

            ServiceGallery::create([
                'service_id' => $service->id,
                'image' => $path,
                'sort_order' => $lastSortOrder
            ]);
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroyGallery(Service $service, ServiceGallery $image)
    {
        try {
            Storage::disk('public')->delete($image->image);
            $image->delete();
            return response()->json(['status' => 'success', 'message' => __('Resim galeriden başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function reorderGallery(Request $request, Service $service)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer'
        ]);

        $order = $request->input('order');
        foreach ($order as $index => $id) {
            ServiceGallery::where('id', $id)
                ->where('service_id', $service->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => __('Sıralama güncellendi.')]);
    }

    public function reorder()
    {
        $services = Service::orderBy('sort_order', 'asc')->get();
        return view('pages.backend.services.reorder', compact('services'));
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
            Service::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => __('Sıralama güncellendi.')]);
    }
}
