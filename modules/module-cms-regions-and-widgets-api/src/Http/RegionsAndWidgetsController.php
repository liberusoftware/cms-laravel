<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\RegionsAndWidgets\Models\Widget;
use Liberu\Cms\RegionsAndWidgets\Services\RegionWidgetService;

final class RegionsAndWidgetsController
{
    public function show(Request $request, string $key, RegionWidgetService $service): JsonResponse
    {
        $data = $request->validate(['context' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->render($key, $data['context'] ?? [], $request->user()?->current_team_id)]);
    }

    public function region(Request $request, RegionWidgetService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string'], 'label' => ['required', 'string'], 'theme' => ['nullable', 'string']]);

        return response()->json(['data' => $service->createRegion($data['key'], $data['label'], $data['theme'] ?? null, $request->user()?->current_team_id)], 201);
    }

    public function widget(Request $request, RegionWidgetService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string'], 'type' => ['required', 'string'], 'title' => ['nullable', 'string'], 'configuration' => ['array']]);

        return response()->json(['data' => $service->createWidget($data['key'], $data['type'], $data['configuration'] ?? [], $data['title'] ?? null, $request->user()?->current_team_id)], 201);
    }

    public function place(Request $request, string $key, RegionWidgetService $service): JsonResponse
    {
        $data = $request->validate(['widget_id' => ['required', 'integer'], 'position' => ['sometimes', 'integer', 'min:0'], 'visibility' => ['array'], 'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date']]);
        $region = $service->region($key, $request->user()?->current_team_id);
        $widget = Widget::query()->whereKey($data['widget_id'])->where('team_id', $request->user()?->current_team_id)->firstOrFail();

        return response()->json(['data' => $service->place($region, $widget, $data['position'] ?? 0, $data['visibility'] ?? [], $data['starts_at'] ?? null, $data['ends_at'] ?? null)], 201);
    }
}
