<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;
use Liberu\Cms\ThemeMarketplace\Models\ThemeInstallation;

final class ThemeMarketplaceQuery
{
    public function catalog(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $term = trim($search);

        return MarketplaceTheme::query()->where('status', 'published')->where('security_status', 'approved')->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$term}%")->orWhere('key', 'like', "%{$term}%")))->withCount('ratings')->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function find(string $key, ?string $version = null): ?MarketplaceTheme
    {
        return MarketplaceTheme::query()->where('key', $key)->when($version !== null, fn ($query) => $query->where('version', $version))->first();
    }

    public function installation(string $siteKey, string $themeKey): ?ThemeInstallation
    {
        return ThemeInstallation::query()->where('site_key', $siteKey)->whereHas('theme', fn ($query) => $query->where('key', $themeKey))->with('theme')->first();
    }
}
