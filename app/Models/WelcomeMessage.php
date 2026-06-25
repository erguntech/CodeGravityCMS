<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToClient;
use Spatie\Translatable\HasTranslations;
use App\Traits\ReturnsApiTranslations;
use App\Traits\AutoTranslates;

class WelcomeMessage extends Model
{
    use BelongsToClient, HasTranslations;
    use AutoTranslates;

    public $translatable = ['title', 'description'];

    protected $table = 'welcome_messages';

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'image',
        'status'
    ];
}
