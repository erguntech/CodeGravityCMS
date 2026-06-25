<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaGallery extends Model
{
    protected $table = 'media_galleries';

    protected $fillable = [
        'media_id',
        'image',
        'sort_order'
    ];

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
