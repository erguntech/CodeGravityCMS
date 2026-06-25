<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

use App\Traits\HasUniqueSlug;

class ProductCategory extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates, HasUniqueSlug;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'client_id',
        'parent_id',
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

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
