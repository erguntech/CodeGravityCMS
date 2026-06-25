<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPostGallery extends Model
{
    protected $table = 'blog_post_galleries';

    protected $fillable = [
        'blog_post_id',
        'image',
        'sort_order'
    ];

    public function blog_post()
    {
        return $this->belongsTo(BlogPost::class);
    }
}
