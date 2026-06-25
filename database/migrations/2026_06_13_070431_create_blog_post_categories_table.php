<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_post_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->json('title');
            $table->string('slug')->unique();
            $table->json('description')->nullable();
            $table->enum('status', ['active', 'passive'])->default('active');
            $table->string('image')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->text('seo_description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('blog_post_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_categories');
    }
};
