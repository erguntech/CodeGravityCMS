<?php

namespace App\Traits;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToClient
{
    protected static function bootBelongsToClient()
    {
        // A Client-role user can only ever write into their own client account.
        // This overrides any client_id that arrives via mass assignment from a form.
        static::saving(function ($model) {
            if (auth()->check() && auth()->user()->hasRole('Client')) {
                $ownClientId = auth()->user()->client?->id;
                if ($ownClientId) {
                    $model->client_id = $ownClientId;
                }
            }
        });

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
