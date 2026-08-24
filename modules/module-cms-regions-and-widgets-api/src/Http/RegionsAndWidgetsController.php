<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\RegionsAndWidgets\Services\RegionWidgetService;

final class RegionsAndWidgetsController
{
    public function show(Request $request, string $key, RegionWidgetService $service): JsonResponse
    {
        $data = $request->validate(['context' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->render($key, $data['context'] ?? [])]);
    }
}
