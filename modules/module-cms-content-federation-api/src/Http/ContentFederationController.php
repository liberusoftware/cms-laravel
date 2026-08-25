<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentFederation\Models\FederationSource;
use Liberu\Cms\ContentFederation\Services\ContentFederationService;

final class ContentFederationController
{
    public function index(Request $request, ContentFederationService $service): JsonResponse
    {
        return response()->json(['data' => $service->sources($request->user()?->current_team_id, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, ContentFederationService $service): JsonResponse
    {
        return response()->json(['data' => $service->source($request->validate(['name' => ['required', 'string', 'max:160'], 'adapter' => ['required', 'string', 'max:80'], 'endpoint' => ['nullable', 'url']]), $request->user()?->current_team_id)], 201);
    }

    public function ingest(Request $request, FederationSource $source, ContentFederationService $service): JsonResponse
    {
        $data = $request->validate(['external_type' => ['required', 'string', 'max:120'], 'external_key' => ['required', 'string', 'max:180'], 'payload' => ['required', 'array'], 'etag' => ['nullable', 'string']]);

        return response()->json(['data' => $service->ingest($source, $data['external_type'], $data['external_key'], $data['payload'], $data['etag'] ?? null)]);
    }

    public function fallback(FederationSource $source, string $type, string $key, ContentFederationService $service): JsonResponse
    {
        return response()->json(['data' => $service->fallback($source, $type, $key)]);
    }
}
