<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\StaticPublishing\Queries\StaticPublishingQuery;
use Liberu\Cms\StaticPublishing\Services\StaticPublishingService;
use Liberu\Cms\StaticPublishingApi\Http\Resources\StaticBuildResource;

final class StaticPublishingController
{
    public function store(Request $request, StaticPublishingService $service): JsonResponse
    {
        $data = $request->validate(['routes' => ['required', 'array'], 'site_key' => ['sometimes', 'nullable', 'string'], 'kind' => ['sometimes', 'string', 'in:full,incremental,preview'], 'deployment' => ['sometimes', 'string']]);

        return response()->json(['data' => StaticBuildResource::make($service->build($data['routes'], $data['site_key'] ?? null, $data['kind'] ?? 'full', $data['deployment'] ?? 'local'))], 201);
    }

    public function index(Request $request, StaticPublishingQuery $query): JsonResponse
    {
        $page = $query->builds($request->integer('per_page', 15));

        return response()->json(['data' => array_map(StaticBuildResource::make(...), $page->items()), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]]);
    }

    public function show(int|string $build, StaticPublishingQuery $query): JsonResponse
    {
        $model = $query->build($build);
        abort_unless($model, 404);

        return response()->json(['data' => StaticBuildResource::make($model)]);
    }

    public function invalidate(Request $request, int|string $build, StaticPublishingQuery $query, StaticPublishingService $service): JsonResponse
    {
        $model = $query->build($build);
        abort_unless($model, 404);
        $data = $request->validate(['path' => ['required', 'string', 'max:2048'], 'reason' => ['sometimes', 'string', 'max:255']]);

        return response()->json(['data' => $service->invalidate($model, $data['path'], $data['reason'] ?? 'content-changed')], 201);
    }

    public function rollback(int|string $build, StaticPublishingQuery $query, StaticPublishingService $service): JsonResponse
    {
        $model = $query->build($build);
        abort_unless($model, 404);

        return response()->json(['data' => StaticBuildResource::make($service->rollback($model))]);
    }

    public function diagnostics(int|string $build, StaticPublishingQuery $query, StaticPublishingService $service): JsonResponse
    {
        $model = $query->build($build);
        abort_unless($model, 404);

        return response()->json(['data' => $service->diagnostics($model)]);
    }

    public function deploy(Request $request, int|string $build, StaticPublishingQuery $query, StaticPublishingService $service): JsonResponse
    {
        $model = $query->build($build);
        abort_unless($model, 404);
        $data = $request->validate(['adapter' => ['required', 'string', 'max:255']]);

        return response()->json(['data' => $service->deploy($model, $data['adapter'])], 202);
    }
}
