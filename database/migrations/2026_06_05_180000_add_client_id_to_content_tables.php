<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'product_categories',
            'products',
            'project_categories',
            'projects',
            'sliders',
            'news',
            'media',
            'references',
            'welcome_messages'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'product_categories',
            'products',
            'project_categories',
            'projects',
            'sliders',
            'news',
            'media',
            'references',
            'welcome_messages'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign([$table . '_client_id_foreign']);
                    $table->dropColumn('client_id');
                });
            }
        }
    }
};
