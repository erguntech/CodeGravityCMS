<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

use App\Traits\HasUniqueSlug;

class Product extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates, HasUniqueSlug;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'client_id',
        'product_category_id',
        'title',
        'slug',
        'description',
        'price',
        'discounted_price',
        'status',
        'image',
        'seo_title',
        'seo_keywords',
        'seo_description',
        'sort_order'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class)->orderBy('sort_order', 'asc');
    }
}
