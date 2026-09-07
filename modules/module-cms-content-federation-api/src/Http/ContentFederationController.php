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
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'adapter' => ['required', 'string', 'max:80'], 'endpoint' => ['nullable', 'url']]);

        return response()->json(['data' => $service->source($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function ingest(Request $request, FederationSource $source, ContentFederationService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['external_type' => ['required', 'string', 'max:120'], 'external_key' => ['required', 'string', 'max:180'], 'payload' => ['required', 'array'], 'etag' => ['nullable', 'string']]));

        abort_unless($source->team_id === $request->user()?->current_team_id, 404);
        $payload = [];
        if (is_array($data['payload'] ?? null)) {
            foreach ($data['payload'] as $key => $value) {
                if (is_string($key)) {
                    $payload[$key] = $value;
                }
            }
        }

        return response()->json(['data' => $service->ingest($source, is_string($data['external_type'] ?? null) ? $data['external_type'] : '', is_string($data['external_key'] ?? null) ? $data['external_key'] : '', $payload, is_string($data['etag'] ?? null) ? $data['etag'] : null)]);
    }

    public function fallback(FederationSource $source, string $type, string $key, ContentFederationService $service): JsonResponse
    {
        abort_unless($source->team_id === request()->user()?->current_team_id, 404);

        return response()->json(['data' => $service->fallback($source, $type, $key)]);
    }

    /** @return array<string, mixed> */
    private function normalized(mixed $value): array
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_string($key)) {
                    $data[$key] = $item;
                }
            }
        }

        return $data;
    }
}
