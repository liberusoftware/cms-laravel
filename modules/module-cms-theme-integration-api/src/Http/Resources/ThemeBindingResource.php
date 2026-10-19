<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationApi\Http\Resources;

use Liberu\Cms\ThemeIntegration\Models\ThemeBinding;

final class ThemeBindingResource
{
    /** @return array<string, mixed> */
    public static function make(?ThemeBinding $binding): ?array
    {
        return $binding instanceof ThemeBinding ? [
            'site_key' => $binding->site_key,
            'channel_key' => $binding->channel_key,
            'theme_key' => $binding->theme_key,
            'fallback_theme_key' => $binding->fallback_theme_key,
            'active' => (bool) $binding->active,
            'preview_expires_at' => $binding->preview_expires_at?->toISOString(),
        ] : null;
    }
}
