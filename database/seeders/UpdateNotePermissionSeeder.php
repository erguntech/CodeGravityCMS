<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateNotePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Spatie\Permission\Models\Permission::create(['name' => 'updates.view', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::create(['name' => 'updates.manage', 'guard_name' => 'web']);

        $admin = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(['updates.view', 'updates.manage']);
        }
    }
}
