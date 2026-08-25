<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationApi\Http\Resources;

use Liberu\Cms\ThemeIntegration\Models\ThemeComponent;

final class ThemeComponentResource
{
    /** @return array<string, mixed> */
    public static function make(ThemeComponent $component): array
    {
        return ['theme_key' => $component->theme_key, 'region' => $component->region, 'component_key' => $component->component_key, 'view_contract' => $component->view_contract ?? [], 'configuration' => $component->configuration ?? []];
    }
}
