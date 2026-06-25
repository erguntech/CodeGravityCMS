<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

use App\Traits\HasUniqueSlug;

class Slider extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates, HasUniqueSlug;

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'client_id',
        'title',
        'slug',
        'description',
        'status',
        'image',
        'sort_order'
    ];
}
