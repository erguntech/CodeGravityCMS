<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Disable Foreign Key Checks for a clean truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tablesToTruncate = [
            'users',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'roles',
            'permissions',
            'activity_log',
            'jobs',
            'cache',
            // Truncate any module tables just to be absolutely sure
            'clients',
            'brands',
            'media',
            'news',
            'products',
            'product_categories',
            'projects',
            'project_categories',
            'references',
            'sliders',
        ];

        foreach ($tablesToTruncate as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Re-enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Seed Clean Spatie Roles & Permissions
        $this->call(RolePermissionSeeder::class);

        // 3. Create EXACTLY ONE Admin User (Core Admin)
        $admin = User::create([
            'name' => 'Core Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123123123'),
            'user_type' => 'Admin',
            'gender' => 'Erkek',
            'status' => 'active'
        ]);

        // 4. Assign Admin Role to user
        $admin->assignRole('Admin');
    }
}