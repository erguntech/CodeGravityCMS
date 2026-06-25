<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UpdateNote;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UpdateNoteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:updates.view', only: ['index', 'show']),
            new Middleware('can:updates.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = UpdateNote::select(['id', 'version', 'status', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
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
                    $canManage = auth()->user()->can('updates.manage');

                    if ($canManage) {
                        $editUrl = route('admin.updates.edit', $row->id);
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

        return view('pages.backend.updates.index');
    }

    public function create()
    {
        return view('pages.backend.updates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|string|max:255',
            'notes' => 'required|string',
            'status' => 'required|in:active,passive',
        ], [], [
            'version' => __('Güncelleme Sürümü'),
            'notes' => __('Güncelleme Notları'),
            'status' => __('Durumu'),
        ]);

        UpdateNote::create($request->only(['version', 'notes', 'status']));

        return redirect()->route('admin.updates.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(UpdateNote $update)
    {
        return view('pages.backend.updates.edit', compact('update'));
    }

    public function update(Request $request, UpdateNote $update)
    {
        $request->validate([
            'version' => 'required|string|max:255',
            'notes' => 'required|string',
            'status' => 'required|in:active,passive',
        ], [], [
            'version' => __('Güncelleme Sürümü'),
            'notes' => __('Güncelleme Notları'),
            'status' => __('Durumu'),
        ]);

        $update->update($request->only(['version', 'notes', 'status']));

        return redirect()->route('admin.updates.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(UpdateNote $update)
    {
        try {
            $update->delete();
            return response()->json(['status' => 'success', 'message' => __('Kayıt başarıyla silindi.')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }
}
