<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Truncate permissions, roles and their relations
        Schema::disableForeignKeyConstraints();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        Schema::enableForeignKeyConstraints();

        $permissions = [
            // Kullanıcı Yönetimi
            'users.view',
            'users.manage',
            'roles.view',
            'roles.manage',
            'permissions.view',
            'permissions.manage',
            'logs.view',

            // Ürün Yönetimi
            'products.view',
            'products.manage',
            'product-categories.view',
            'product-categories.manage',

            // Proje Yönetimi
            'projects.view',
            'projects.manage',
            'project-categories.view',
            'project-categories.manage',

            // Blog Yönetimi
            'blogposts.view',
            'blogposts.manage',
            'blog-post-categories.view',
            'blog-post-categories.manage',
            // Hizmet Yönetimi
            'services.view',
            'services.manage',
            'service-categories.view',
            'service-categories.manage',

            // Diğer Modüller
            'sliders.view',
            'sliders.manage',
            'news.view',
            'news.manage',
            'media.view',
            'media.manage',
            'references.view',
            'references.manage',
            'brands.view',
            'brands.manage',
            'welcome_message.view',
            'welcome_message.manage',

            // Müşteri
            'clients.view',
            'clients.manage',

            // API & Ayarlar
            'api_access.view',
            'systemsettings.view',
            'systemsettings.manage',
        ];

        // 2. Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // 3. Create roles
        $adminRole = Role::create(['name' => 'Admin']);
        $clientRole = Role::create(['name' => 'Client']);

        // 4. Assign permissions to Client
        $clientPermissions = Permission::whereIn('name', [
            'products.view',
            'products.manage',
            'product-categories.view',
            'product-categories.manage',
            'projects.view',
            'projects.manage',
            'project-categories.view',
            'project-categories.manage',
            'blogposts.view',
            'blogposts.manage',
            'blog-post-categories.view',
            'blog-post-categories.manage',
            'services.view',
            'services.manage',
            'service-categories.view',
            'service-categories.manage',
            'sliders.view',
            'sliders.manage',
            'news.view',
            'news.manage',
            'media.view',
            'media.manage',
            'references.view',
            'references.manage',
            'brands.view',
            'brands.manage',
            'welcome_message.view',
            'welcome_message.manage',
            'api_access.view',
        ])->get();
        $clientRole->syncPermissions($clientPermissions);

        // 5. Assign permissions to Admin
        $adminPermissions = Permission::whereNotIn('name', [
            'products.view',
            'products.manage',
            'product-categories.view',
            'product-categories.manage',
            'projects.view',
            'projects.manage',
            'project-categories.view',
            'project-categories.manage',
            'blogposts.view',
            'blogposts.manage',
            'blog-post-categories.view',
            'blog-post-categories.manage',
            'services.view',
            'services.manage',
            'service-categories.view',
            'service-categories.manage',
            'sliders.view',
            'sliders.manage',
            'news.view',
            'news.manage',
            'media.view',
            'media.manage',
            'references.view',
            'references.manage',
            'brands.view',
            'brands.manage',
            'welcome_message.view',
            'welcome_message.manage',
        ])->get();
        // Since api_access.view is not in the excluded list, Admin gets it automatically.
        // systemsettings.view and systemsettings.manage are also granted to Admin automatically.
        $adminRole->syncPermissions($adminPermissions);

        // 6. Assign roles to users based on user_type
        $users = User::all();
        foreach ($users as $user) {
            $roleName = match ($user->user_type) {
                'Admin' => 'Admin',
                'Client' => 'Client',
                default => null,
            };

            if ($roleName) {
                $user->syncRoles([$roleName]);
            }
        }
    }
}
