<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

use App\Traits\HasUniqueSlug;

class Project extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates, HasUniqueSlug;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'client_id',
        'project_category_id',
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
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function gallery()
    {
        return $this->hasMany(ProjectGallery::class)->orderBy('sort_order', 'asc');
    }
}
