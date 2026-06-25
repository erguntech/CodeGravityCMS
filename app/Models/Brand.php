<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

use App\Traits\HasUniqueSlug;

class Brand extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates, HasUniqueSlug;

    public $translatable = ['title', 'description'];

    protected $table = 'brands';

    protected $fillable = [
        'client_id',
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

    public function gallery()
    {
        return $this->hasMany(BrandGallery::class, 'brand_id')->orderBy('sort_order', 'asc');
    }
}
