<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilderApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\LayoutBuilder\Models\Layout;
use Liberu\Cms\LayoutBuilder\Services\LayoutBuilderService;

final class LayoutBuilderController
{
    public function show(string $targetType, string $targetId, LayoutBuilderService $service): JsonResponse
    {
        return response()->json(['data' => $service->resolve($targetType, $targetId)]);
    }

    public function store(Request $request, LayoutBuilderService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:180'], 'target_type' => ['required', 'string', 'max:80'], 'target_id' => ['required', 'string', 'max:255'], 'definition' => ['required', 'array']]);

        return response()->json(['data' => $service->create($data)], 201);
    }

    public function publish(string $layout, LayoutBuilderService $service): JsonResponse
    {
        return response()->json(['data' => $service->publish(Layout::query()->findOrFail($layout))]);
    }
}
