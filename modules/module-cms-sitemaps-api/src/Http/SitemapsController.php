<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\Sitemaps\Services\SitemapService;
use Liberu\Cms\SitemapsApi\Http\Resources\SitemapEntryResource;

final class SitemapsController
{
    public function index(Request $request, SitemapService $service): JsonResponse
    {
        $data = $request->validate(['site_id' => ['sometimes', 'nullable', 'integer'], 'type' => ['sometimes', 'nullable', 'string'], 'locale' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => array_map(SitemapEntryResource::make(...), $service->entries($data['site_id'] ?? null, $data['type'] ?? null, $data['locale'] ?? null))]);
    }

    public function create(Request $request, SitemapService $service): JsonResponse { $data = $request->validate(['url' => ['required', 'url', 'max:2048'], 'site_id' => ['sometimes', 'nullable', 'integer'], 'type' => ['sometimes', 'string', 'max:50'], 'locale' => ['sometimes', 'nullable', 'string', 'max:20'], 'priority' => ['sometimes', 'numeric', 'between:0,1'], 'extensions' => ['sometimes', 'array']]); return response()->json(['data' => SitemapEntryResource::make($service->add($data['url'], $data['site_id'] ?? null, $data['type'] ?? 'web', $data['locale'] ?? null, (float) ($data['priority'] ?? .5), $data['extensions'] ?? []))], 201); }
    public function exclude(Request $request, SitemapService $service): JsonResponse { $data = $request->validate(['url' => ['required', 'url'], 'site_id' => ['sometimes', 'nullable', 'integer']]); return response()->json(['data' => ['updated' => $service->exclude($data['url'], $data['site_id'] ?? null)]]); }
    public function notify(Request $request, SitemapService $service): JsonResponse { $data = $request->validate(['engine' => ['required', 'in:google,bing'], 'site_id' => ['sometimes', 'nullable', 'integer']]); return response()->json(['data' => $service->notify($data['engine'], $data['site_id'] ?? null)], 202); }
    public function chunks(Request $request, SitemapService $service): JsonResponse { $data = $request->validate(['site_id' => ['sometimes', 'nullable', 'integer'], 'type' => ['sometimes', 'nullable', 'string'], 'locale' => ['sometimes', 'nullable', 'string'], 'size' => ['sometimes', 'integer', 'min:1', 'max:50000']]); return response()->json(['data' => array_map(fn (array $chunk): array => array_map(SitemapEntryResource::make(...), $chunk), $service->chunks($data['site_id'] ?? null, (int) ($data['size'] ?? 50000), $data['type'] ?? null, $data['locale'] ?? null))]); }
}
