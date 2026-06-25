<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenceGallery extends Model
{
    protected $table = 'reference_galleries';

    protected $fillable = [
        'reference_id',
        'image',
        'sort_order'
    ];

    public function reference()
    {
        return $this->belongsTo(Reference::class, 'reference_id');
    }
}
