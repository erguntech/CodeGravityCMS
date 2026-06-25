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
        $tables = ['product_categories', 'projects', 'project_categories', 'sliders', 'news', 'media', 'references'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableSchema) {
                $tableSchema->integer('sort_order')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['product_categories', 'projects', 'project_categories', 'sliders', 'news', 'media', 'references'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $tableSchema) {
                $tableSchema->dropColumn('sort_order');
            });
        }
    }
};
