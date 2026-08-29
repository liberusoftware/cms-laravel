<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\ExtensionMarketplace\Models\ExtensionListing;

final class ExtensionMarketplaceQuery
{
    public function catalog(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return ExtensionListing::query()->where('status', 'published')->where('security_status', 'approved')->with(['publisher', 'category'])->withCount('reviews')->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('key', 'like', "%{$term}%")))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key): ?ExtensionListing
    {
        return ExtensionListing::query()->where('key', $key)->with(['publisher', 'category', 'versions', 'support'])->first();
    }
}
