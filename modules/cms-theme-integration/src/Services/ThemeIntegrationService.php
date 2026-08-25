<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ThemeIntegration\Models\ThemeBinding;
use Liberu\Cms\ThemeIntegration\Models\ThemeComponent;
use Liberu\Cms\ThemeIntegration\Queries\ThemeIntegrationQuery;

final class ThemeIntegrationService
{
    public function __construct(private readonly ThemeIntegrationQuery $query) {}

    public function bind(string $siteKey, ?string $channelKey, string $themeKey, string $fallbackThemeKey = 'default', ?int $teamId = null): ThemeBinding
    {
        foreach (['siteKey' => $siteKey, 'themeKey' => $themeKey, 'fallbackThemeKey' => $fallbackThemeKey] as $field => $value) {
            if (trim($value) === '') {
                throw ValidationException::withMessages([$field => 'Theme keys are required.']);
            }
        }
        if ($channelKey !== null && trim($channelKey) === '') {
            throw ValidationException::withMessages(['channelKey' => 'Channel keys cannot be empty.']);
        }
        if ($themeKey === $fallbackThemeKey) {
            throw ValidationException::withMessages(['fallbackThemeKey' => 'A theme cannot fall back to itself.']);
        }

        return ThemeBinding::query()->updateOrCreate(['site_key' => $siteKey, 'channel_key' => $channelKey, 'team_id' => $teamId], ['theme_key' => $themeKey, 'fallback_theme_key' => $fallbackThemeKey, 'active' => true, 'team_id' => $teamId]);
    }

    public function updateBinding(ThemeBinding $binding, array $attributes): ThemeBinding
    {
        $updated = $this->bind(
            (string) ($attributes['site_key'] ?? $binding->site_key),
            array_key_exists('channel_key', $attributes) ? $attributes['channel_key'] : $binding->channel_key,
            (string) ($attributes['theme_key'] ?? $binding->theme_key),
            (string) ($attributes['fallback_theme_key'] ?? $binding->fallback_theme_key),
            $binding->team_id,
        );

        if (array_key_exists('active', $attributes)) {
            $updated->update(['active' => (bool) $attributes['active']]);
        }

        return $updated->refresh();
    }

    public function removeBinding(ThemeBinding $binding): void
    {
        DB::transaction(fn (): ?bool => $binding->delete());
    }

    public function select(?string $siteKey, ?string $channelKey = null, ?int $teamId = null): ?ThemeBinding
    {
        return $siteKey === null ? null : $this->query->binding($siteKey, $channelKey, $teamId);
    }

    public function effectiveTheme(?string $siteKey, ?string $channelKey = null, ?int $teamId = null): string
    {
        $binding = $this->select($siteKey, $channelKey, $teamId);

        return $binding?->theme_key ?? $binding?->fallback_theme_key ?? 'default';
    }

    public function registerComponent(string $themeKey, string $region, string $componentKey, array $viewContract = [], array $configuration = [], ?int $teamId = null): ThemeComponent
    {
        if (trim($themeKey) === '' || trim($region) === '' || trim($componentKey) === '') {
            throw ValidationException::withMessages(['component_key' => 'Region and component key are required.']);
        }

        return ThemeComponent::query()->updateOrCreate(['theme_key' => $themeKey, 'region' => $region, 'component_key' => $componentKey, 'team_id' => $teamId], ['view_contract' => $viewContract, 'configuration' => $configuration, 'team_id' => $teamId]);
    }

    /** @return array<int, ThemeComponent> */
    public function components(string $themeKey, ?string $region = null, ?int $teamId = null): array
    {
        return $this->query->components($themeKey, $region, $teamId);
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
