<?php

namespace App\Traits;

trait HasUniqueSlug
{
    /**
     * Boot the trait.
     */
    public static function bootHasUniqueSlug()
    {
        static::saving(function ($model) {
            if ($model->isDirty('slug') && !empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->slug, $model);
            }
        });
    }

    /**
     * Return $baseSlug if it is free, otherwise the first free "$baseSlug-N".
     *
     * The slug column carries a global UNIQUE index, so the lookup must bypass
     * every global scope (e.g. the per-client scope from BelongsToClient).
     * Otherwise rows owned by other clients stay invisible to the check and
     * the insert fails with a duplicate-key error.
     */
    public static function generateUniqueSlug(string $baseSlug, $ignore = null): string
    {
        $query = static::withoutGlobalScopes()
            ->where(function ($q) use ($baseSlug) {
                $q->where('slug', $baseSlug)
                  ->orWhere('slug', 'like', $baseSlug . '-%');
            });

        if ($ignore && $ignore->exists) {
            $query->where($ignore->getKeyName(), '!=', $ignore->getKey());
        }

        $taken = $query->pluck('slug')->flip();

        if (!$taken->has($baseSlug)) {
            return $baseSlug;
        }

        $count = 1;
        while ($taken->has("{$baseSlug}-{$count}")) {
            $count++;
        }

        return "{$baseSlug}-{$count}";
    }
}
