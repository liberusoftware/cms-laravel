<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ThemeIntegration\Models\ThemeBinding;
use Liberu\Cms\ThemeIntegration\Models\ThemeComponent;

final class ThemeIntegrationService
{
    public function bind(string $siteKey, ?string $channelKey, string $themeKey, string $fallbackThemeKey = 'default', ?int $teamId = null): ThemeBinding
    {
        return ThemeBinding::query()->updateOrCreate(['site_key' => $siteKey, 'channel_key' => $channelKey, 'team_id' => $teamId], ['theme_key' => $themeKey, 'fallback_theme_key' => $fallbackThemeKey, 'active' => true, 'team_id' => $teamId]);
    }

    public function select(?string $siteKey, ?string $channelKey = null, ?int $teamId = null): ?ThemeBinding
    {
        return ThemeBinding::query()->where('active', true)->where('team_id', $teamId)->where(fn ($q) => $q->where('site_key', $siteKey)->where('channel_key', $channelKey)->orWhere(fn ($q) => $q->where('site_key', $siteKey)->whereNull('channel_key')))->first();
    }

    public function effectiveTheme(?string $siteKey, ?string $channelKey = null, ?int $teamId = null): string
    {
        return $this->select($siteKey, $channelKey, $teamId)?->theme_key ?? $this->select($siteKey, null, $teamId)?->fallback_theme_key ?? 'default';
    }

    public function registerComponent(string $themeKey, string $region, string $componentKey, array $viewContract = [], array $configuration = [], ?int $teamId = null): ThemeComponent
    {
        if ($region === '' || $componentKey === '') {
            throw ValidationException::withMessages(['component_key' => 'Region and component key are required.']);
        }

        return ThemeComponent::query()->updateOrCreate(['theme_key' => $themeKey, 'region' => $region, 'component_key' => $componentKey, 'team_id' => $teamId], ['view_contract' => $viewContract, 'configuration' => $configuration, 'team_id' => $teamId]);
    }

    /** @return array<int, ThemeComponent> */
    public function components(string $themeKey, ?string $region = null, ?int $teamId = null): array
    {
        return ThemeComponent::query()->where('theme_key', $themeKey)->where('team_id', $teamId)->when($region, fn ($q) => $q->where('region', $region))->orderBy('region')->orderBy('component_key')->get()->all();
    }

    public function previewToken(ThemeBinding $binding): string
    {
        $binding->update(['preview_token' => Str::random(48)]);

        return (string) $binding->preview_token;
    }

    public function preview(ThemeBinding $binding, string $token): bool
    {
        return hash_equals((string) $binding->preview_token, $token);
    }
}
