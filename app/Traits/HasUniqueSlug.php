<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /**
     * Boot the trait.
     */
    public static function bootHasUniqueSlug()
    {
        static::saving(function ($model) {
            // Check if slug is dirty and not empty
            if ($model->isDirty('slug') && !empty($model->slug)) {
                $originalSlug = $model->slug;
                $slug = $originalSlug;
                $count = 1;

                while (true) {
                    $query = static::where('slug', $slug);
                    if ($model->exists) {
                        $query->where('id', '!=', $model->id);
                    }

                    if (!$query->exists()) {
                        $model->slug = $slug;
                        break;
                    }

                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }
            }
        });
    }
}
