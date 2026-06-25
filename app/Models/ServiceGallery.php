<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceGallery extends Model
{
    protected $table = 'service_galleries';

    protected $fillable = [
        'service_id',
        'image',
        'sort_order'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
