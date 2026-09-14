<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

abstract class Controller
{
    /**
     * "exists" rule limited to rows that belong to the authenticated user's client.
     * Admins (no client) fall back to a plain exists check.
     */
    protected function existsForClient(string $table, string $column = 'id'): Exists
    {
        $rule = Rule::exists($table, $column);
        $clientId = auth()->user()?->client?->id;

        if ($clientId) {
            $rule->where('client_id', $clientId);
        }

        return $rule;
    }
}
