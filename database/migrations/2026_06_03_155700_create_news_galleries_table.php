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
        Schema::create('news_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('image');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert Spatie Permissions for News Management
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $viewPerm = Permission::firstOrCreate(['name' => 'news.view']);
        $managePerm = Permission::firstOrCreate(['name' => 'news.manage']);

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
        Schema::dropIfExists('news_galleries');

        // Delete Permissions
        Permission::whereIn('name', ['news.view', 'news.manage'])->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
