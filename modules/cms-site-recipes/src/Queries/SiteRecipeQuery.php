<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteRecipes\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\SiteRecipes\Models\SiteRecipe;

final class SiteRecipeQuery
{
    public function recipes(int $perPage = 15, bool $publishedOnly = false): LengthAwarePaginator
    {
        return SiteRecipe::query()->when($publishedOnly, fn ($q) => $q->where('status', 'published'))->withCount('versions')->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key, bool $publishedOnly = false): ?SiteRecipe
    {
        return SiteRecipe::query()->where('key', $key)->when($publishedOnly, fn ($q) => $q->where('status', 'published'))->first();
    }
}
