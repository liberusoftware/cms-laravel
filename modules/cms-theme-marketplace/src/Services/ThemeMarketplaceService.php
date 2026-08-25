<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace\Services;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;
use Liberu\Cms\ThemeMarketplace\Models\ThemeInstallation;
use Liberu\Cms\ThemeMarketplace\Models\ThemeRating;

final class ThemeMarketplaceService
{
    public function publish(array $manifest, ?int $teamId = null): MarketplaceTheme
    {
        foreach (['key', 'name', 'version', 'author'] as $required) {
            if (! is_string($manifest[$required] ?? null) || $manifest[$required] === '') {
                throw ValidationException::withMessages([$required => 'Theme manifest field is required.']);
            }
        }
        if (! preg_match('/^\\d+\\.\\d+\\.\\d+([-.][0-9A-Za-z.-]+)?$/', $manifest['version'])) {
            throw ValidationException::withMessages(['version' => 'Theme versions must use semantic versioning.']);
        }
        if (isset($manifest['preview_url']) && filter_var($manifest['preview_url'], FILTER_VALIDATE_URL) === false) {
            throw ValidationException::withMessages(['preview_url' => 'Preview URLs must be valid URLs.']);
        }
        if (($manifest['parent_key'] ?? null) === $manifest['key']) {
            throw ValidationException::withMessages(['parent_key' => 'A theme cannot inherit from itself.']);
        }

        return MarketplaceTheme::query()->updateOrCreate(['key' => $manifest['key'], 'version' => $manifest['version'], 'team_id' => $teamId], ['name' => $manifest['name'], 'author' => $manifest['author'], 'description' => $manifest['description'] ?? null, 'manifest' => $manifest, 'compatibility' => $manifest['compatibility'] ?? [], 'preview_url' => $manifest['preview_url'] ?? null, 'license' => $manifest['license'] ?? 'proprietary', 'parent_key' => $manifest['parent_key'] ?? null, 'status' => 'published', 'security_status' => 'pending', 'team_id' => $teamId]);
    }

    public function reviewSecurity(MarketplaceTheme $theme, string $status): MarketplaceTheme
    {
        if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
            throw ValidationException::withMessages(['security_status' => 'Unsupported security review status.']);
        }
        $theme->update(['security_status' => $status]);

        return $theme->refresh();
    }

    public function compatible(MarketplaceTheme $theme, string $cmsVersion, array $features = []): bool
    {
        if ($theme->security_status !== 'approved') {
            return false;
        }
        $requirements = $theme->compatibility ?? [];
        if (($requirements['cms'] ?? null) !== null && ! version_compare($cmsVersion, (string) $requirements['cms'], '>=')) {
            return false;
        }

        return array_diff($requirements['features'] ?? [], $features) === [];
    }

    public function install(MarketplaceTheme $theme, string $siteKey, string $cmsVersion, array $features = [], ?int $teamId = null): ThemeInstallation
    {
        if (! $this->compatible($theme, $cmsVersion, $features)) {
            throw ValidationException::withMessages(['theme' => 'Theme is not security-approved or compatible with this CMS.']);
        }

        return ThemeInstallation::query()->updateOrCreate(['theme_id' => $theme->id, 'site_key' => $siteKey, 'team_id' => $teamId], ['installed_version' => $theme->version, 'status' => 'installed', 'installed_at' => now(), 'team_id' => $teamId]);
    }

    public function update(ThemeInstallation $installation, MarketplaceTheme $theme, string $cmsVersion, array $features = []): ThemeInstallation
    {
        if ($theme->key !== $installation->theme->key || ! $this->compatible($theme, $cmsVersion, $features)) {
            throw ValidationException::withMessages(['theme' => 'Update is incompatible with the installed theme.']);
        }
        if (version_compare($theme->version, $installation->installed_version, '<=')) {
            throw ValidationException::withMessages(['theme' => 'Theme updates must increase the installed version.']);
        }
        DB::transaction(fn () => $installation->update(['theme_id' => $theme->id, 'updated_at_version' => $theme->version, 'installed_version' => $theme->version]));

        return $installation->refresh();
    }

    public function rate(MarketplaceTheme $theme, string $reviewerType, int|string $reviewerId, int $rating, ?string $review = null, ?int $teamId = null): ThemeRating
    {
        if ($rating < 1 || $rating > 5) {
            throw ValidationException::withMessages(['rating' => 'Ratings must be between 1 and 5.']);
        }

        return ThemeRating::query()->updateOrCreate(['theme_id' => $theme->id, 'reviewer_type' => $reviewerType, 'reviewer_id' => (string) $reviewerId, 'team_id' => $teamId], ['rating' => $rating, 'review' => $review, 'team_id' => $teamId]);
    }

    /** @return array{average: float, count: int} */
    public function ratingSummary(MarketplaceTheme $theme): array
    {
        return ['average' => round((float) $theme->ratings()->avg('rating'), 2), 'count' => $theme->ratings()->count()];
    }
}
