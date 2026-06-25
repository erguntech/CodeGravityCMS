<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

use App\Traits\HasUniqueSlug;

class BlogPost extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates, HasUniqueSlug;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'client_id',
        'blog_post_category_id',
        'title',
        'slug',
        'description',
        'status',
        'image',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'sort_order'
    ];

    public function category()
    {
        return $this->belongsTo(BlogPostCategory::class, 'blog_post_category_id');
    }

    public function gallery()
    {
        return $this->hasMany(BlogPostGallery::class)->orderBy('sort_order', 'asc');
    }
}
