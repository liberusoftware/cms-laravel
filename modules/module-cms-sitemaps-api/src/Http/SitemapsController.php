<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Sitemaps\Services\SitemapService;

final class SitemapsController
{
    public function index(Request $request, SitemapService $service): JsonResponse
    {
        $data = $request->validate(['site_id' => ['sometimes', 'nullable', 'integer'], 'type' => ['sometimes', 'nullable', 'string'], 'locale' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => $service->entries($data['site_id'] ?? null, $data['type'] ?? null, $data['locale'] ?? null)]);
    }
}
