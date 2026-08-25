<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;
use Liberu\Cms\ThemeMarketplace\Queries\ThemeMarketplaceQuery;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;
use Liberu\Cms\ThemeMarketplaceApi\Http\Resources\MarketplaceThemeResource;
use Liberu\Cms\ThemeMarketplaceApi\Http\Resources\ThemeInstallationResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MarketplaceController
{
    public function index(Request $request, ThemeMarketplaceService $service, ThemeMarketplaceQuery $themes): JsonResponse
    {
        $data = $request->validate(['search' => ['sometimes', 'nullable', 'string'], 'status' => ['sometimes', 'string']]);
        $catalog = $themes->catalog((int) $request->integer('per_page', 15), (string) ($data['search'] ?? ''));
        $catalog->getCollection()->transform(fn (MarketplaceTheme $theme): array => ['key' => $theme->key, 'name' => $theme->name, 'version' => $theme->version, 'author' => $theme->author, 'preview_url' => $theme->preview_url, 'license' => $theme->license, 'security_status' => $theme->security_status, 'rating' => $service->ratingSummary($theme)]);

        return response()->json(['data' => $catalog->items(), 'meta' => ['current_page' => $catalog->currentPage(), 'last_page' => $catalog->lastPage(), 'per_page' => $catalog->perPage(), 'total' => $catalog->total()]]);
    }

    public function show(string $key, ThemeMarketplaceQuery $themes): MarketplaceThemeResource
    {
        $theme = $themes->find($key);
        if (! $theme || $theme->status !== 'published' || $theme->security_status !== 'approved') {
            throw new NotFoundHttpException;
        }

        return new MarketplaceThemeResource($theme);
    }

    public function publish(Request $request, ThemeMarketplaceService $service): MarketplaceThemeResource
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:255'], 'name' => ['required', 'string', 'max:255'], 'version' => ['required', 'string', 'max:64'], 'author' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'manifest' => ['sometimes', 'array'], 'compatibility' => ['sometimes', 'array'], 'preview_url' => ['nullable', 'url'], 'license' => ['nullable', 'string', 'max:100'], 'parent_key' => ['nullable', 'string', 'max:255']]);

        return new MarketplaceThemeResource($service->publish([...$data, ...($data['manifest'] ?? [])]));
    }

    public function install(Request $request, string $key, ThemeMarketplaceQuery $themes, ThemeMarketplaceService $service): ThemeInstallationResource
    {
        $theme = $themes->find($key, $request->input('version'));
        if (! $theme) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['site_key' => ['required', 'string', 'max:255'], 'cms_version' => ['required', 'string', 'max:64'], 'features' => ['sometimes', 'array']]);

        return new ThemeInstallationResource($service->install($theme, $data['site_key'], $data['cms_version'], $data['features'] ?? []));
    }

    public function rate(Request $request, string $key, ThemeMarketplaceQuery $themes, ThemeMarketplaceService $service): MarketplaceThemeResource
    {
        $theme = $themes->find($key);
        if (! $theme) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['reviewer_type' => ['required', 'string', 'max:100'], 'reviewer_id' => ['required', 'string', 'max:255'], 'rating' => ['required', 'integer', 'between:1,5'], 'review' => ['nullable', 'string', 'max:5000']]);
        $service->rate($theme, $data['reviewer_type'], $data['reviewer_id'], $data['rating'], $data['review'] ?? null);

        return new MarketplaceThemeResource($theme->refresh());
    }

    public function security(Request $request, string $key, ThemeMarketplaceQuery $themes, ThemeMarketplaceService $service): MarketplaceThemeResource
    {
        $theme = $themes->find($key);
        if (! $theme) {
            throw new NotFoundHttpException;
        }
        $data = $request->validate(['security_status' => ['required', 'in:pending,approved,rejected']]);

        return new MarketplaceThemeResource($service->reviewSecurity($theme, $data['security_status']));
    }
}
