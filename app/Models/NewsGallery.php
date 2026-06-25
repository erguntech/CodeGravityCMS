<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsGallery extends Model
{
    protected $table = 'news_galleries';

    protected $fillable = [
        'news_id',
        'image',
        'sort_order'
    ];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }
}
