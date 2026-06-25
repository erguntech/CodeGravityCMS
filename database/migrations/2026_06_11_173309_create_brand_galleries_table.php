<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('image');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert Spatie Permissions for References Management
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $viewPerm = Permission::firstOrCreate(['name' => 'brands.view']);
        $managePerm = Permission::firstOrCreate(['name' => 'brands.manage']);

        // Assign to Admin Role
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo([$viewPerm, $managePerm]);
        }

        // Assign to Client Role
        $client = Role::where('name', 'Client')->first();
        if ($client) {
            $client->givePermissionTo([$viewPerm, $managePerm]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_galleries');

        // Delete Permissions
        Permission::whereIn('name', ['brands.view', 'brands.manage'])->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
