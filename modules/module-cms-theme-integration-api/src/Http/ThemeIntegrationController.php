<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ThemeIntegration\Services\ThemeIntegrationService;

final class ThemeIntegrationController
{
    public function show(Request $request, ThemeIntegrationService $service): JsonResponse
    {
        $data = $request->validate(['site_key' => ['required', 'string'], 'channel_key' => ['sometimes', 'nullable', 'string'], 'region' => ['sometimes', 'nullable', 'string']]);
        $binding = $service->select($data['site_key'], $data['channel_key'] ?? null);
        $theme = $service->effectiveTheme($data['site_key'], $data['channel_key'] ?? null);

        return response()->json(['data' => ['theme_key' => $theme, 'binding' => $binding?->only(['site_key', 'channel_key', 'theme_key', 'fallback_theme_key']), 'components' => $service->components($theme, $data['region'] ?? null)]], 200);
    }
}
