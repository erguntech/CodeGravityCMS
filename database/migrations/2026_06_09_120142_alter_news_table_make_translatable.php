<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First migrate data directly in DB via raw queries to prevent data loss
        DB::table('news')->get()->each(function ($news) {
            DB::table('news')
                ->where('id', $news->id)
                ->update([
                    'title' => json_encode(['tr' => $news->title]),
                    'description' => json_encode(['tr' => $news->description]),
                ]);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('description')->nullable()->change();
        });

        DB::table('news')->get()->each(function ($news) {
            $title = json_decode($news->title, true);
            $desc = json_decode($news->description, true);
            
            DB::table('news')
                ->where('id', $news->id)
                ->update([
                    'title' => $title['tr'] ?? (is_array($title) ? reset($title) : $news->title),
                    'description' => $desc['tr'] ?? (is_array($desc) ? reset($desc) : $news->description),
                ]);
        });
    }
};
