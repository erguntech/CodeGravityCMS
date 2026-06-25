<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'client_id',
        'ip_address',
        'user_agent',
        'url',
        'visited_at'
    ];
}
