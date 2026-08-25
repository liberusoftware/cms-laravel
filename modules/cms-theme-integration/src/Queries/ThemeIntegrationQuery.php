<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Cms\ThemeIntegration\Models\ThemeBinding;
use Liberu\Cms\ThemeIntegration\Models\ThemeComponent;
use Liberu\Cms\Contracts\Tenancy\TenantContextInterface;

final class ThemeIntegrationQuery
{
    public function __construct(private readonly TenantContextInterface $tenant) {}

    private function tenantId(?int $teamId): int|string|null
    {
        return $teamId ?? $this->tenant->tenantId();
    }

    public function bindings(int $perPage = 15, ?int $teamId = null): LengthAwarePaginator
    {
        return ThemeBinding::query()->where('team_id', $this->tenantId($teamId))->latest()->paginate(max(1, min(100, $perPage)));
    }

    public function binding(string $siteKey, ?string $channelKey = null, ?int $teamId = null): ?ThemeBinding
    {
        return ThemeBinding::query()->where('active', true)->where('team_id', $this->tenantId($teamId))
            ->where('site_key', $siteKey)
            ->where(fn ($query) => $query->where('channel_key', $channelKey)->orWhereNull('channel_key'))
            ->orderByRaw('CASE WHEN channel_key IS NULL THEN 1 ELSE 0 END')->first();
    }

    public function bindingById(int|string $id, ?int $teamId = null): ?ThemeBinding
    {
        return ThemeBinding::query()->whereKey($id)->where('team_id', $this->tenantId($teamId))->first();
    }

    /** @return array<int, ThemeComponent> */
    public function components(string $themeKey, ?string $region = null, ?int $teamId = null): array
    {
        return ThemeComponent::query()->where('theme_key', $themeKey)->where('team_id', $this->tenantId($teamId))
            ->when($region !== null, fn ($query) => $query->where('region', $region))
            ->orderBy('region')->orderBy('component_key')->get()->all();
    }
}
