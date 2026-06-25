<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:roles.view', only: ['index', 'show']),
            new Middleware('can:roles.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::select(['id', 'name', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id', function($row){
                    return '<span class="badge badge-light-primary fw-bold">#'.$row->id.'</span>';
                })
                ->addColumn('permissions', function($row){
                    $badges = '';
                    foreach ($row->permissions as $permission) {
                        $badges .= '<span class="badge badge-light-primary fw-bold me-1">'.$permission->name.'</span>';
                    }
                    return $badges ?: '-';
                })
                ->editColumn('created_at', function($row){
                    return $row->created_at->format('d.m.Y H:i');
                })
                ->addColumn('action', function($row){
                    $canManage = auth()->user()->can('roles.manage');
                    if ($canManage) {
                        $editUrl = route('admin.roles.edit', $row->id);
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1';
                        
                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm delete-role" data-id="'.$row->id.'" title="Sil">
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
                ->rawColumns(['id', 'permissions', 'action'])
                ->make(true);
        }

        return view('pages.backend.roles.index');
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('pages.backend.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'nullable|array'
        ], [], [
            'name' => 'Rol Adı',
            'permissions' => 'İzinler'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'roles.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('pages.backend.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array'
        ], [], [
            'name' => 'Rol Adı',
            'permissions' => 'İzinler'
        ]);

        $role->name = $request->name;
        $role->save();
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'roles.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response()->json(['status' => 'success', 'message' => 'Kayıt sistemden başarıyla silindi.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Bir hata oluştu!']);
        }
    }
}
