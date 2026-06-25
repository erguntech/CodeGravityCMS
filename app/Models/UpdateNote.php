<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'notes',
        'status',
    ];
}
