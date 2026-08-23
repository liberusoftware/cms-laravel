<?php

declare(strict_types=1);

namespace Liberu\Cms\Content\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Generates URL slugs that are unique within a model's table, appending a
 * numeric suffix on collision. Shared by every content type that has a slug.
 */
final class Slugger
{
    public static function unique(Model $model, string $value, string $column = 'slug'): string
    {
        $base = Str::slug($value) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while (self::exists($model, $slug, $column)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function exists(Model $model, string $slug, string $column): bool
    {
        $query = $model->newQuery()->where($column, $slug);

        if ($model->exists) {
            $query->whereKeyNot($model->getKey());
        }

        return $query->exists();
    }
}
