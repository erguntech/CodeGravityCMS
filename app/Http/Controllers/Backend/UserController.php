<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:users.view', only: ['index', 'show']),
            new Middleware('can:users.manage', except: ['index', 'show']),
        ];
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select(['id', 'name', 'email', 'user_type', 'status', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('id', function ($row) {
                    return '<span class="badge badge-light-primary fw-bold">#' . $row->id . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d.m.Y H:i');
                })

                ->editColumn('user_type', function ($row) {
                    $badges = [
                        'Admin' => 'primary',
                        'Moderator' => 'info',
                        'Agency' => 'success',
                        'Independent Model' => 'warning',
                        'Agency Model' => 'dark',
                        'Visitor' => 'secondary'
                    ];

                    $userType = $row->user_type;


                    $badgeClass = $badges[$userType] ?? 'info';
                    $labelText = $userType === 'Agency Model' ? __('Ajans Modeli') : __($userType ?? 'Belirtilmedi');
                    return '<span class="badge badge-light-' . $badgeClass . ' fw-bold">' . $labelText . '</span>';
                })
                ->editColumn('status', function ($row) {
                    $status = $row->status ?? 'active';
                    $badgeClass = $status === 'active' ? 'success' : 'danger';
                    $label = $status === 'active' ? __('Aktif') : __('Pasif');
                    return '<span class="badge badge-light-' . $badgeClass . ' fw-bold">' . $label . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $canManage = auth()->user()->can('users.manage');
                    $isCurrentUser = $row->id === auth()->id();

                    if ($canManage) {
                        $editUrl = route('admin.users.edit', $row->id);
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1';

                        $deleteBtn = $isCurrentUser
                            ? '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm disabled opacity-50" title="' . __('Kendinizi silemezsiniz') . '">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                               </a>'
                            : '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm delete-user" data-id="' . $row->id . '" title="' . __('Sil') . '">
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
                ->rawColumns(['id', 'user_type', 'status', 'action'])
                ->make(true);
        }

        return view('pages.backend.users.index');
    }

    public function create()
    {
        $roles = \App\Models\Role::whereIn('name', ['Admin'])->get();
        return view('pages.backend.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|string|in:active,passive',
            'roles' => 'required|array'
        ], [], [
            'name' => __('Ad Soyad'),
            'email' => __('E-posta'),
            'password' => __('Şifre'),
            'status' => __('Durum')
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'user_type' => 'Admin',
            'status' => $request->status ?? 'active',
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'users.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function show(User $user)
    {
        $user->load(['roles', 'permissions']);
        return view('pages.backend.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $isEditableType = in_array($user->user_type, ['Admin', 'Moderator']);
        if ($isEditableType) {
            $roles = \App\Models\Role::whereIn('name', ['Admin'])->get();
        } else {
            $roles = \App\Models\Role::all();
        }
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('pages.backend.users.edit', compact('user', 'roles', 'userRoles', 'isEditableType'));
    }

    public function update(Request $request, User $user)
    {
        $isEditableType = in_array($user->user_type, ['Admin', 'Moderator']);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|string|in:active,passive',
            'roles' => $isEditableType ? 'required|array' : 'nullable',
            'roles.*' => 'in:Admin'
        ], [], [
            'name' => __('Ad Soyad'),
            'email' => __('E-posta'),
            'password' => __('Şifre'),
            'status' => __('Durum')
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        if ($isEditableType) {
            if ($request->has('roles')) {
                $user->syncRoles($request->roles);
            } else {
                $user->syncRoles([]);
            }
        }

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'users.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['status' => 'error', 'message' => __('Kendi hesabınızı silemezsiniz!')]);
        }

        try {
            $user->delete();
            return response()->json(['status' => 'success', 'message' => 'Kayıt sistemden başarıyla silindi.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }
}
