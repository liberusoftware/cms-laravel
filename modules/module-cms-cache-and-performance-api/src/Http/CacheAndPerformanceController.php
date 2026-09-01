<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\CacheAndPerformance\Services\CacheAndPerformanceService;

final class CacheAndPerformanceController
{
    public function remember(Request $request, CacheAndPerformanceService $service): JsonResponse
    {
        $raw = $request->validate(['cache_key' => ['required', 'string', 'max:255'], 'cache_type' => ['required', 'string'], 'ttl_seconds' => ['required', 'integer'], 'tags' => ['sometimes', 'array'], 'metadata' => ['sometimes', 'array'], 'value' => ['sometimes']]);
        $data = is_array($raw) ? $raw : [];
        $tags = is_array($data['tags'] ?? null) ? array_values(array_filter($data['tags'], static fn (mixed $tag): bool => is_string($tag))) : [];
        $metadata = [];
        if (is_array($data['metadata'] ?? null)) {
            foreach ($data['metadata'] as $key => $value) {
                if (is_string($key)) {
                    $metadata[$key] = $value;
                }
            }
        }
        $result = $service->remember($request->user()?->current_team_id, is_string($data['cache_key'] ?? null) ? $data['cache_key'] : '', is_string($data['cache_type'] ?? null) ? $data['cache_type'] : '', is_int($data['ttl_seconds'] ?? null) ? $data['ttl_seconds'] : 0, fn () => $data['value'] ?? null, $tags, $metadata);

        return response()->json(['data' => ['entry' => $result['entry'], 'hit' => $result['hit']]]);
    }

    public function invalidate(Request $request, CacheAndPerformanceService $service): JsonResponse
    {
        $raw = $request->validate(['tags' => ['sometimes', 'array'], 'keys' => ['sometimes', 'array'], 'idempotency_key' => ['required', 'string', 'max:255']]);
        $data = is_array($raw) ? $raw : [];

        $tags = is_array($data['tags'] ?? null) ? array_values(array_filter($data['tags'], static fn (mixed $tag): bool => is_string($tag))) : [];
        $keys = is_array($data['keys'] ?? null) ? array_values(array_filter($data['keys'], static fn (mixed $key): bool => is_string($key))) : [];

        return response()->json(['data' => $service->invalidate($request->user()?->current_team_id, $tags, $keys, is_string($data['idempotency_key'] ?? null) ? $data['idempotency_key'] : '')]);
    }

    public function diagnostics(CacheAndPerformanceService $service): JsonResponse
    {
        return response()->json(['data' => $service->diagnostics(request()->user()?->current_team_id)]);
    }
}
