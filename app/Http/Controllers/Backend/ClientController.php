<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:clients.view', only: ['index', 'show']),
            new Middleware('can:clients.manage', except: ['index', 'show']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('client')->where('user_type', 'Client')->select(['id', 'name', 'email', 'status', 'created_at']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('company_name', function ($row) {
                    return $row->client->company_name ?? '-';
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
                    $canManage = auth()->user()->can('clients.manage');
                    
                    if ($canManage) {
                        $routePrefix = 'admin.';
                        $editUrl = route($routePrefix . 'clients.edit', $row->id);
                        $sizesUrl = route($routePrefix . 'clients.client-settings', $row->id);
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1';
                        $sizesClass = 'btn btn-icon btn-light-info btn-sm me-1';

                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm delete-user" data-id="' . $row->id . '" title="' . __('Sil') . '">
                                    <i class="ki-duotone ki-trash fs-2">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                               </a>';
                    } else {
                        $editUrl = 'javascript:void(0)';
                        $sizesUrl = 'javascript:void(0)';
                        $editClass = 'btn btn-icon btn-light-warning btn-sm me-1 unauthorized-action-btn';
                        $sizesClass = 'btn btn-icon btn-light-info btn-sm me-1 unauthorized-action-btn';

                        $deleteBtn = '<a href="javascript:void(0)" class="btn btn-icon btn-light-danger btn-sm unauthorized-action-btn" title="' . __('Sil') . '">
                                <i class="ki-duotone ki-trash fs-2">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                </i>
                           </a>';
                    }

                    $btn = '<div class="d-flex justify-content-end flex-shrink-0">
                                <a href="' . $sizesUrl . '" class="' . $sizesClass . '" title="' . __('Müşteri Ayarları') . '">
                                    <i class="ki-duotone ki-setting-2 fs-2">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                </a>
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

        return view('pages.backend.clients.index');
    }

    public function create()
    {
        return view('pages.backend.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|string|in:active,passive',
            'company_name' => 'required|string|max:255',
            'languages' => 'required|array|min:1',
            'languages.*' => 'string',
        ], [], [
            'name' => __('Ad Soyad'),
            'email' => __('E-posta'),
            'password' => __('Şifre'),
            'status' => __('Durum'),
            'company_name' => __('İşletme Adı'),
            'avatar' => __('İşletme Görseli')
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'Client',
                'status' => $request->status ?? 'active',
            ]);

            $user->assignRole('Client');

            $client = Client::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
            ]);

            if ($request->has('languages')) {
                foreach ($request->languages as $index => $lang) {
                    $client->languages()->create([
                        'code' => $lang,
                        'is_default' => $index === 0, // First language is default
                        'is_active' => true,
                    ]);
                }
            }

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                \App\Helpers\ImageHelper::resizeAndCrop(\Illuminate\Support\Facades\Storage::disk('public')->path($path), 500, 500);
                $user->avatar = $path;
                $user->save();
            }
        });

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'clients.index')->with('success', __('Kayıt başarı ile sisteme eklendi.'));
    }

    public function show(User $client)
    {
        // Parameter name is $client, but it represents the User model
        $user = $client;
        if ($user->user_type !== 'Client') {
            abort(404);
        }
        
        return view('pages.backend.clients.show', compact('user'));
    }

    public function edit(User $client)
    {
        $user = $client;
        if ($user->user_type !== 'Client') {
            abort(404);
        }

        return view('pages.backend.clients.edit', compact('user'));
    }

    public function update(Request $request, User $client)
    {
        $user = $client;
        if ($user->user_type !== 'Client') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|string|in:active,passive',
            'company_name' => 'required|string|max:255',
            'languages' => 'required|array|min:1',
            'languages.*' => 'string',
        ], [], [
            'name' => __('Ad Soyad'),
            'email' => __('E-posta'),
            'password' => __('Şifre'),
            'status' => __('Durum'),
            'company_name' => __('İşletme Adı'),
            'avatar' => __('İşletme Görseli'),
            'languages' => __('Dil Seçimi')
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
            \App\Helpers\ImageHelper::resizeAndCrop(\Illuminate\Support\Facades\Storage::disk('public')->path($user->avatar), 500, 500);
        }

        $user->save();

        if ($user->client) {
            $user->client->update([
                'company_name' => $request->company_name,
            ]);
            $clientModel = $user->client;
        } else {
            $clientModel = Client::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
            ]);
        }

        if ($request->has('languages')) {
            $currentLangs = $clientModel->languages()->pluck('code')->toArray();
            $newLangs = $request->languages;
            
            // Delete removed languages
            $clientModel->languages()->whereNotIn('code', $newLangs)->delete();
            
            // Add new languages
            foreach ($newLangs as $index => $lang) {
                if (!in_array($lang, $currentLangs)) {
                    $clientModel->languages()->create([
                        'code' => $lang,
                        'is_default' => $clientModel->languages()->count() === 0,
                        'is_active' => true,
                    ]);
                }
            }
            
            // Ensure at least one default exists if there are languages
            if ($clientModel->languages()->count() > 0 && !$clientModel->languages()->where('is_default', true)->exists()) {
                $clientModel->languages()->first()->update(['is_default' => true]);
            }
        } else {
            $clientModel->languages()->delete();
        }

        $routePrefix = 'admin.';
        return redirect()->route($routePrefix . 'clients.index')->with('success', __('Kayıt başarı ile güncellendi.'));
    }

    public function destroy(User $client)
    {
        $user = $client;
        if ($user->user_type !== 'Client') {
            return response()->json(['status' => 'error', 'message' => __('Sadece müşteriler silinebilir!')]);
        }

        try {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $user->delete(); // This will cascade delete the client record due to foreign key constraints
            return response()->json(['status' => 'success', 'message' => 'Müşteri sistemden başarıyla silindi.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => __('Bir hata oluştu!')]);
        }
    }



    public function clientSettings(User $client)
    {
        $user = $client;
        if ($user->user_type !== 'Client') {
            abort(404);
        }

        return view('pages.backend.clients.client_settings', compact('user'));
    }

    public function updateClientSettings(Request $request, User $client)
    {
        $user = $client;
        if ($user->user_type !== 'Client') {
            abort(404);
        }

        // Resim boyutları validasyonu
        $imageSizeModules = [
            'slider', 'news', 'reference', 'brand',
            'welcome_message', 'product', 'product_category',
            'project', 'project_category', 'blog_post', 'blog_post_category',
            'service', 'service_category'
        ];
        $rules = [];
        $attributes = [];
        foreach ($imageSizeModules as $key) {
            $rules["image_sizes.{$key}"] = 'nullable|string|regex:/^\d+x\d+$/';
        }

        $request->validate($rules, [], $attributes);

        if ($user->client) {
            $updateData = [];

            if ($request->has('domain')) $updateData['domain'] = $request->input('domain');
            if ($request->has('domain_started_at')) $updateData['domain_started_at'] = $request->input('domain_started_at') ?: null;
            if ($request->has('domain_expires_at')) $updateData['domain_expires_at'] = $request->input('domain_expires_at') ?: null;
            if ($request->has('ssl_started_at')) $updateData['ssl_started_at'] = $request->input('ssl_started_at') ?: null;
            if ($request->has('ssl_expires_at')) $updateData['ssl_expires_at'] = $request->input('ssl_expires_at') ?: null;

            if ($request->has('address')) $updateData['address'] = $request->input('address');
            if ($request->has('additional_address')) $updateData['additional_address'] = $request->input('additional_address');
            if ($request->has('phone')) $updateData['phone'] = $request->input('phone');
            if ($request->has('fax')) $updateData['fax'] = $request->input('fax');
            if ($request->has('mobile')) $updateData['mobile'] = $request->input('mobile');
            if ($request->has('additional_contact')) $updateData['additional_contact'] = $request->input('additional_contact');
            if ($request->has('coordinates')) $updateData['coordinates'] = $request->input('coordinates');

            if ($request->has('instagram')) $updateData['instagram'] = $request->input('instagram');
            if ($request->has('facebook')) $updateData['facebook'] = $request->input('facebook');
            if ($request->has('whatsapp')) $updateData['whatsapp'] = $request->input('whatsapp');

            if ($request->has('google_analytics_code')) $updateData['google_analytics_code'] = $request->input('google_analytics_code');

            if ($request->has('image_sizes')) {
                // To prevent deleting other existing image sizes when we only submit a partial form, 
                // we should merge with existing sizes, or just assume the form submits ALL sizes.
                // In our view, the image_sizes form contains ALL image sizes, so replacing is fine.
                $updateData['image_sizes'] = array_filter($request->input('image_sizes', []));
            }

            if ($request->has('is_modules_form')) {
                $updateData['modules'] = $request->input('modules', []);
            }

            if (!empty($updateData)) {
                $user->client->update($updateData);
            }
        }

        return redirect()->back()->with('success', __('Kayıt başarı ile güncellendi.'));
    }
}
