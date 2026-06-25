<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Reference;
use App\Models\ReferenceGallery;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ReferenceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:references.view', only: ['index', 'show']),
            new Middleware('can:references.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
            $data = Reference::select(['id', 'title', 'status', 'image', 'created_at', 'sort_order']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('title', function ($row) use ($clientDefaultLang) {
                    $titleData = $row->getTranslations('title');
                    return $titleData[$clientDefaultLang] ?? reset($titleData);
                })
                ->editColumn('id', function ($row) {
                    return '<span class="badge badge-light-primary fw-bold">#' . $row->id . '</span>';
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
                    $canManage = auth()->user()->can('references.manage');
                    
                    $viewBtn = '';
                    if ($row->image) {
                        $viewBtn = '<a data-fslightbox="references-gallery" data-type="image" href="' . asset('storage/' . $row->image) . '" class="btn btn-icon btn-light-primary btn-sm me-1" title="' . __('Resmi Görüntüle') . '">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                   </a>';
                    }

                    if ($canManage) {
                        $routePrefix = 'client.';
                        $editUrl = route($routePrefix . 'references.edit', $row->id);
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

                    $btn = '<div class="d-flex justify-content-end flex-shrink-0">
                                ' . $viewBtn . '
                               
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

        return view('pages.backend.references.index');
    }

    public function create()
    {
        return view('pages.backend.references.create');
    }

    public function store(Request $request)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'status' => 'required|in:active,passive',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Referans Başlığı'),
            'title.' . $clientDefaultLang => __('Referans Başlığı'),
            'description' => __('Referans Açıklaması'),
            'status' => __('Durumu'),
            'image' => __('Referans Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', 'image']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('references', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('reference');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $data['sort_order'] = Reference::max('sort_order') + 1;
        Reference::create($data);

        return redirect()->route('client.references.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(Reference $reference)
    {
        return view('pages.backend.references.edit', compact('reference'));
    }

    public function update(Request $request, Reference $reference)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Referans Başlığı'),
            'title.' . $clientDefaultLang => __('Referans Başlığı'),
            'description' => __('Referans Açıklaması'),
            'status' => __('Durumu'),
            'image' => __('Referans Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', 'image']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        if ($request->hasFile('image')) {
            if ($reference->image) {
                Storage::disk('public')->delete($reference->image);
            }
            $data['image'] = $request->file('image')->store('references', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('reference');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $reference->update($data);

        return redirect()->route('client.references.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(Reference $reference)
    {
        try {
            if ($reference->image) {
                Storage::disk('public')->delete($reference->image);
            }
            // Delete gallery images
            foreach ($reference->gallery as $galleryItem) {
                Storage::disk('public')->delete($galleryItem->image);
                $galleryItem->delete();
            }
            $reference->delete();
            return response()->json(['status' => 'success', 'message' => __('Referans başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function gallery(Reference $reference)
    {
        $gallery = $reference->gallery;
        return view('pages.backend.references.gallery', compact('reference', 'gallery'));
    }

    public function storeGallery(Request $request, Reference $reference)
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

        $lastSortOrder = $reference->gallery()->max('sort_order') ?? 0;
        foreach ($request->file('images') as $file) {
            $lastSortOrder++;
            $path = $file->store('references/gallery', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('reference_gallery');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($path), $size['width'], $size['height']);
                }
            }

            ReferenceGallery::create([
                'reference_id' => $reference->id,
                'image' => $path,
                'sort_order' => $lastSortOrder
            ]);
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroyGallery(Reference $reference, ReferenceGallery $image)
    {
        try {
            Storage::disk('public')->delete($image->image);
            $image->delete();
            return response()->json(['status' => 'success', 'message' => __('Resim galeriden başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function reorderGallery(Request $request, Reference $reference)
    {
        $order = $request->input('order', []);
        foreach ($order as $sortOrder => $id) {
            ReferenceGallery::where('id', $id)->where('reference_id', $reference->id)->update([
                'sort_order' => $sortOrder + 1
            ]);
        }
        return response()->json(['status' => 'success', 'message' => __('Sıralama başarıyla güncellendi.')]);
    }

    public function reorder()
    {
        $items = Reference::orderBy('sort_order', 'asc')->get();
        return view('pages.backend.references.reorder', compact('items'));
    }

    public function updateOrder(Request $request)
    {
        $order = $request->input('order', []);
        foreach ($order as $sortOrder => $id) {
            Reference::where('id', $id)->update([
                'sort_order' => $sortOrder + 1
            ]);
        }
        return response()->json(['status' => 'success']);
    }
}
