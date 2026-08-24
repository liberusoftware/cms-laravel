<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;

final class MarketplaceController
{
    public function index(Request $request, ThemeMarketplaceService $service): JsonResponse
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string'], 'status' => ['sometimes', 'string']]);
        $themes = MarketplaceTheme::query()->where('status', $data['status'] ?? 'published')->when($data['search'] ?? null, fn ($q, string $search) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('key', 'like', "%{$search}%")))->withCount('ratings')->latest()->get()->map(fn (MarketplaceTheme $theme): array => ['key' => $theme->key, 'name' => $theme->name, 'version' => $theme->version, 'author' => $theme->author, 'preview_url' => $theme->preview_url, 'license' => $theme->license, 'security_status' => $theme->security_status, 'rating' => $service->ratingSummary($theme)]);

        return response()->json(['data' => $themes]);
    }
}
