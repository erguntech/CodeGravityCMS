<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandGallery extends Model
{
    protected $table = 'brand_galleries';

    protected $fillable = [
        'brand_id',
        'image',
        'sort_order'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
