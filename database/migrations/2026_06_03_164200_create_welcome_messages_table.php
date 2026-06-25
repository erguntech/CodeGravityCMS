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
        Schema::create('welcome_messages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'passive'])->default('active');
            $table->timestamps();
        });

        // Insert Spatie Permissions for Welcome Message Management
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $viewPerm = Permission::firstOrCreate(['name' => 'welcome_message.view']);
        $managePerm = Permission::firstOrCreate(['name' => 'welcome_message.manage']);

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
        Schema::dropIfExists('welcome_messages');

        // Delete Permissions
        Permission::whereIn('name', ['welcome_message.view', 'welcome_message.manage'])->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
