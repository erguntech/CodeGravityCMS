<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectCategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:project-categories.view', only: ['index', 'show']),
            new Middleware('can:project-categories.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
            $data = ProjectCategory::with('parent')->select(['id', 'title', 'parent_id', 'status', 'image', 'created_at', 'sort_order']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('parent_name', function ($row) use ($clientDefaultLang) {
                    if ($row->parent) {
                        $parentTitleData = $row->parent->getTranslations('title');
                        $parentTitle = $parentTitleData[$clientDefaultLang] ?? reset($parentTitleData);
                        return '<span class="badge badge-light-primary fw-bold">' . $parentTitle . '</span>';
                    }
                    return '<span class="badge badge-light-info fw-bold">' . __('Ana Kategori') . '</span>';
                })
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
                    $canManage = auth()->user()->can('project-categories.manage');
                    
                    $viewBtn = '';
                    if ($row->image) {
                        $viewBtn = '<a data-fslightbox="project-categories-gallery" data-type="image" href="' . asset('storage/' . $row->image) . '" class="btn btn-icon btn-light-primary btn-sm me-1" title="' . __('Resmi Görüntüle') . '">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                   </a>';
                    }

                    if ($canManage) {
                        $routePrefix = 'client.';
                        $editUrl = route($routePrefix . 'project-categories.edit', $row->id);
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
                ->rawColumns(['id', 'status', 'parent_name', 'action'])
                ->make(true);
        }
 
        return view('pages.backend.project_categories.index');
    }

    public function create()
    {
        $categories = ProjectCategory::whereNull('parent_id')->get();
        return view('pages.backend.project_categories.create', compact('categories'));
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
            'parent_id' => 'nullable|exists:project_categories,id',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Proje Kategorisi Başlığı'),
            'title.' . $clientDefaultLang => __('Proje Kategorisi Başlığı'),
            'description' => __('Proje Kategorisi Açıklaması'),
            'parent_id' => __('Üst Kategori'),
            'status' => __('Durumu'),
            'image' => __('Proje Kategorisi Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', 'image']);
        $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('project_categories', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('project_category');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $data['sort_order'] = ProjectCategory::max('sort_order') + 1;
        ProjectCategory::create($data);

        return redirect()->route('client.project-categories.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(ProjectCategory $projectCategory)
    {
        $categories = ProjectCategory::whereNull('parent_id')->where('id', '!=', $projectCategory->id)->get();
        return view('pages.backend.project_categories.edit', compact('projectCategory', 'categories'));
    }

    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $clientDefaultLang = auth()->user()->client?->languages()->where('is_default', true)->first()?->code ?? 'tr';
        $request->validate([
            'title' => 'required|array',
            'title.' . $clientDefaultLang => 'required|string|max:255',
            'title.*' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'parent_id' => 'nullable|exists:project_categories,id',
            'status' => 'required|in:active,passive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [], [
            'title' => __('Proje Kategorisi Başlığı'),
            'title.' . $clientDefaultLang => __('Proje Kategorisi Başlığı'),
            'description' => __('Proje Kategorisi Açıklaması'),
            'parent_id' => __('Üst Kategori'),
            'status' => __('Durumu'),
            'image' => __('Proje Kategorisi Manşet Resmi'),
            'seo_title' => __('SEO Meta Başlığı'),
            'seo_keywords' => __('SEO Meta Etiketleri'),
            'seo_description' => __('SEO Meta Açıklaması'),
        ]);

        $data = $request->except(['_token', '_method', 'image']);
        
        if ($request->title !== $projectCategory->title) {
            $titles = $request->input('title');
        $firstTitle = is_array($titles) ? reset($titles) : $titles;
        $data['slug'] = Str::slug($firstTitle);
        }

        if ($request->hasFile('image')) {
            if ($projectCategory->image) {
                Storage::disk('public')->delete($projectCategory->image);
            }
            $data['image'] = $request->file('image')->store('project_categories', 'public');

            $client = auth()->user()->client;
            if ($client) {
                $size = $client->getImageSize('project_category');
                if ($size) {
                    \App\Helpers\ImageHelper::resizeAndCrop(Storage::disk('public')->path($data['image']), $size['width'], $size['height']);
                }
            }
        }

        $projectCategory->update($data);

        return redirect()->route('client.project-categories.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(ProjectCategory $projectCategory)
    {
        try {
            if ($projectCategory->image) {
                Storage::disk('public')->delete($projectCategory->image);
            }
            $projectCategory->delete();
            return response()->json(['status' => 'success', 'message' => __('Kategori başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }

    public function reorder()
    {
        $categories = ProjectCategory::whereNull('parent_id')
            ->with(['children' => function($q) {
                $q->orderBy('sort_order', 'asc');
            }])
            ->orderBy('sort_order', 'asc')
            ->get();
        return view('pages.backend.project_categories.reorder', compact('categories'));
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
            ProjectCategory::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success', 'message' => __('Sıralama güncellendi.')]);
    }
}
