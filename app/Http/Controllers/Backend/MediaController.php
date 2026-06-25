<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaGallery;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:media.view', only: ['index', 'show']),
            new Middleware('can:media.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
            $data = Media::select(['id', 'title', 'status', 'image', 'created_at', 'sort_order']);
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
                    $canManage = auth()->user()->can('media.manage');
                    
                    $viewBtn = '';

                    $galleryBtn = '';
                    if ($canManage) {
                        $galleryUrl = route('client.media.gallery', $row->id);
                        $galleryBtn = '<a href="' . $galleryUrl . '" class="btn btn-icon btn-light-info btn-sm me-1" title="' . __('Galeri') . '">
                                            <i class="ki-duotone ki-picture fs-2">
                                                <span class="path1"></span><span class="path2"></span>
                                            </i>
                                       </a>';
                    }

                    if ($canManage) {
                        $routePrefix = 'client.';
                        $editUrl = route($routePrefix . 'media.edit', $row->id);
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

        return view('pages.backend.media.index');
    }

    public function create()
    {
        return view('pages.backend.media.create');
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
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Medya Başlığı'),
            'title.' . $clientDefaultLang => __('Medya Başlığı'),
            'description' => __('Medya Açıklaması'),
            'status' => __('Durumu'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        $data['sort_order'] = Media::max('sort_order') + 1;
        Media::create($data);

        return redirect()->route('client.media.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(Media $media)
    {
        return view('pages.backend.media.edit', compact('media'));
    }

    public function update(Request $request, Media $media)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'status' => 'required|in:active,passive',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Medya Başlığı'),
            'title.' . $clientDefaultLang => __('Medya Başlığı'),
            'description' => __('Medya Açıklaması'),
            'status' => __('Durumu'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        $media->update($data);

        return redirect()->route('client.media.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(Media $media)
    {
        try {
            if ($media->image) {
                Storage::disk('public')->delete($media->image);
            }
            // Delete gallery images
            foreach ($media->gallery as $galleryItem) {
                Storage::disk('public')->delete($galleryItem->image);
                $galleryItem->delete();
            }
            $media->delete();
            return response()->json(['status' => 'success', 'message' => __('Medya Galerisi başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function gallery(Media $media)
    {
        $gallery = $media->gallery;
        return view('pages.backend.media.gallery', compact('media', 'gallery'));
    }

    public function storeGallery(Request $request, Media $media)
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

        $lastSortOrder = $media->gallery()->max('sort_order') ?? 0;
        foreach ($request->file('images') as $file) {
            $lastSortOrder++;
            $path = $file->store('media/gallery', 'public');



            MediaGallery::create([
                'media_id' => $media->id,
                'image' => $path,
                'sort_order' => $lastSortOrder
            ]);
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroyGallery(Media $media, MediaGallery $image)
    {
        try {
            Storage::disk('public')->delete($image->image);
            $image->delete();
            return response()->json(['status' => 'success', 'message' => __('Resim galeriden başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function reorderGallery(Request $request, Media $media)
    {
        $order = $request->input('order', []);
        foreach ($order as $sortOrder => $id) {
            MediaGallery::where('id', $id)->where('media_id', $media->id)->update([
                'sort_order' => $sortOrder + 1
            ]);
        }
        return response()->json(['status' => 'success', 'message' => __('Sıralama başarıyla güncellendi.')]);
    }

    public function reorder()
    {
        $media = Media::orderBy('sort_order', 'asc')->get();
        return view('pages.backend.media.reorder', compact('media'));
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
            Media::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => __('Sıralama güncellendi.')]);
    }
}
