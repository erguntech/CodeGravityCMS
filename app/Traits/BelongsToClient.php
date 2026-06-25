<?php

namespace App\Traits;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToClient
{
    protected static function bootBelongsToClient()
    {
        static::creating(function ($model) {
            if (empty($model->client_id) && auth()->check() && auth()->user()->client) {
                $model->client_id = auth()->user()->client->id;
            }
        });

        static::addGlobalScope('client', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Client')) {
                $builder->where($builder->getQuery()->from . '.client_id', auth()->user()->client->id);
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
