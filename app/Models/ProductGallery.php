<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    protected $table = 'product_galleries';

    protected $fillable = [
        'product_id',
        'image',
        'sort_order'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
