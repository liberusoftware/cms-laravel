<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\Core\Models\ContentAlias;
use Liberu\Cms\Core\Models\Site;

/**
 * Public read boundary for the CMS Core capability.
 *
 * Presentation packages depend on this service instead of reaching into Core
 * models themselves. Tenant scopes remain attached to the models, so every
 * consumer observes the same tenant isolation rules.
 */
final class CoreQueryService
{
    public function sites(int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        return Site::query()
            ->with('channels')
            ->when(trim($search) !== '', function ($query) use ($search): void {
                $term = '%'.addcslashes(trim($search), '%_\\').'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('key', 'like', $term));
            })
            ->latest()
            ->paginate($this->perPage($perPage));
    }

    public function site(string $key): ?Site
    {
        return Site::query()->with('channels')->where('key', $key)->first();
    }

    public function channels(string $siteKey, int $perPage = 15, string $search = ''): LengthAwarePaginator
    {
        $site = Site::query()->where('key', $siteKey)->firstOrFail();

        return $site->channels()
            ->when(trim($search) !== '', function ($query) use ($search): void {
                $term = '%'.addcslashes(trim($search), '%_\\').'%';
                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('key', 'like', $term));
            })
            ->latest()
            ->paginate($this->perPage($perPage));
    }

    public function alias(string $siteKey, string $alias): ?ContentAlias
    {
        return ContentAlias::query()
            ->whereRelation('site', 'key', $siteKey)
            ->where('alias', '/'.ltrim(rawurldecode($alias), '/'))
            ->first();
    }

    public function aliases(string $siteKey, int $perPage = 15): LengthAwarePaginator
    {
        $site = Site::query()->where('key', $siteKey)->firstOrFail();

        return $site->aliases()->latest()->paginate($this->perPage($perPage));
    }

    public function identities(string $siteKey, int $perPage = 15): LengthAwarePaginator
    {
        $site = Site::query()->where('key', $siteKey)->firstOrFail();

        return $site->identities()->latest()->paginate($this->perPage($perPage));
    }

    public function settings(string $siteKey, int $perPage = 15, string $environment = 'production'): LengthAwarePaginator
    {
        $site = Site::query()->where('key', $siteKey)->firstOrFail();

        return $site->cmsSettings()
            ->where('environment', $environment)
            ->latest()
            ->paginate($this->perPage($perPage));
    }

    private function perPage(int $perPage): int
    {
        $max = config('cms-api.pagination.max', 100);
        $max = is_numeric($max) ? (int) $max : 100;

        return max(1, min($perPage, $max));
    }
}
