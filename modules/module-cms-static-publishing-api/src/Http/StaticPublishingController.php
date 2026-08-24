<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\StaticPublishing\Services\StaticPublishingService;

final class StaticPublishingController
{
    public function store(Request $request, StaticPublishingService $service): JsonResponse
    {
        $data = $request->validate(['routes' => ['required', 'array'], 'site_key' => ['sometimes', 'nullable', 'string'], 'kind' => ['sometimes', 'string', 'in:full,incremental,preview'], 'deployment' => ['sometimes', 'string']]);

        return response()->json(['data' => $service->build($data['routes'], $data['site_key'] ?? null, $data['kind'] ?? 'full', $data['deployment'] ?? 'local')], 201);
    }
}
