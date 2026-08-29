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
        $data = $request->validate(['cache_key' => ['required', 'string', 'max:255'], 'cache_type' => ['required', 'string'], 'ttl_seconds' => ['required', 'integer'], 'tags' => ['sometimes', 'array'], 'metadata' => ['sometimes', 'array']]);
        $result = $service->remember(null, $data['cache_key'], $data['cache_type'], (int) $data['ttl_seconds'], fn () => $data['value'] ?? null, $data['tags'] ?? [], $data['metadata'] ?? []);

        return response()->json(['data' => ['entry' => $result['entry'], 'hit' => $result['hit']]]);
    }

    public function invalidate(Request $request, CacheAndPerformanceService $service): JsonResponse
    {
        $data = $request->validate(['tags' => ['sometimes', 'array'], 'keys' => ['sometimes', 'array'], 'idempotency_key' => ['required', 'string', 'max:255']]);

        return response()->json(['data' => $service->invalidate(null, $data['tags'] ?? [], $data['keys'] ?? [], $data['idempotency_key'])]);
    }

    public function diagnostics(CacheAndPerformanceService $service): JsonResponse
    {
        return response()->json(['data' => $service->diagnostics(null)]);
    }
}
