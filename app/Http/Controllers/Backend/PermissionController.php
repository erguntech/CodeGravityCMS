<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:permissions.view', only: ['index', 'show']),
            new Middleware('can:permissions.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::select(['id', 'name', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id', function($row){
                    return '<span class="badge badge-light-primary fw-bold">#'.$row->id.'</span>';
                })
                ->editColumn('name', function($row){
                    return '<span class="badge badge-light-info fw-bold">'.$row->name.'</span>';
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d.m.Y H:i');
                })
                ->addColumn('action', function($row){
                    $canManage = auth()->user()->can('permissions.manage');
                    if ($canManage) {
                        $routePrefix = 'admin.';
                        $editUrl = route($routePrefix . 'permissions.edit', $row->id);
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1';
                        
                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm delete-permission" data-id="'.$row->id.'" title="Sil">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                               </a>';
                    } else {
                        $editUrl = 'javascript:void(0)';
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1 unauthorized-action-btn';
                        
                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm unauthorized-action-btn" title="Sil">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                               </a>';
                    }

                    $btn = '<div class="d-flex justify-content-end flex-shrink-0">
                                <a href="'.$editUrl.'" class="'.$editClass.'" title="Düzenle">
                                    <i class="ki-duotone ki-pencil fs-2">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                </a>
                                ' . $deleteBtn . '
                            </div>';
                    return $btn;
                })
                ->rawColumns(['id', 'name', 'action'])
                ->make(true);
        }

        return view('pages.backend.permissions.index');
    }

    public function create()
    {
        return view('pages.backend.permissions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions'
        ], [], [
            'name' => 'İzin Adı'
        ]);

        Permission::create(['name' => $request->name]);

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'permissions.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(Permission $permission)
    {
        return view('pages.backend.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id
        ], [], [
            'name' => 'İzin Adı'
        ]);

        $permission->name = $request->name;
        $permission->save();

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'permissions.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return response()->json(['status' => 'success', 'message' => 'Kayıt sistemden başarıyla silindi.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Bir hata oluştu!']);
        }
    }
}
