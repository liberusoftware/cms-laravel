<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ThemeIntegration\Models\ThemeBinding;
use Liberu\Cms\ThemeIntegration\Services\ThemeIntegrationService;
use Liberu\Cms\ThemeIntegration\Queries\ThemeIntegrationQuery;
use Liberu\Cms\ThemeIntegrationApi\Http\Resources\ThemeBindingResource;
use Liberu\Cms\ThemeIntegrationApi\Http\Resources\ThemeComponentResource;

final class ThemeIntegrationController
{
    public function index(Request $request, ThemeIntegrationQuery $query): JsonResponse
    {
        $bindings = $query->bindings($request->integer('per_page', 15));
        return response()->json(['data' => array_map(ThemeBindingResource::make(...), $bindings->items()), 'meta' => ['current_page' => $bindings->currentPage(), 'last_page' => $bindings->lastPage(), 'per_page' => $bindings->perPage(), 'total' => $bindings->total()]]);
    }

    public function create(Request $request, ThemeIntegrationService $service): JsonResponse
    {
        $data = $request->validate(['site_key' => ['required', 'string', 'max:100'], 'channel_key' => ['sometimes', 'nullable', 'string', 'max:100'], 'theme_key' => ['required', 'string', 'max:100'], 'fallback_theme_key' => ['sometimes', 'string', 'max:100'], 'active' => ['sometimes', 'boolean']]);
        $binding = $service->bind($data['site_key'], $data['channel_key'] ?? null, $data['theme_key'], $data['fallback_theme_key'] ?? 'default');
        if (array_key_exists('active', $data)) $binding->update(['active' => $data['active']]);
        return response()->json(['data' => ThemeBindingResource::make($binding->refresh())], 201);
    }

    public function binding(ThemeBinding $binding): JsonResponse
    {
        return response()->json(['data' => ThemeBindingResource::make($binding)]);
    }

    public function update(Request $request, ThemeBinding $binding, ThemeIntegrationService $service): JsonResponse
    {
        $data = $request->validate(['site_key' => ['sometimes', 'string', 'max:100'], 'channel_key' => ['sometimes', 'nullable', 'string', 'max:100'], 'theme_key' => ['sometimes', 'string', 'max:100'], 'fallback_theme_key' => ['sometimes', 'string', 'max:100'], 'active' => ['sometimes', 'boolean']]);
        return response()->json(['data' => ThemeBindingResource::make($service->updateBinding($binding, $data))]);
    }

    public function delete(ThemeBinding $binding, ThemeIntegrationService $service): JsonResponse
    {
        $service->removeBinding($binding);
        return response()->json([], 204);
    }

    public function show(Request $request, ThemeIntegrationService $service, ThemeIntegrationQuery $query): JsonResponse
    {
        $data = $request->validate(['site_key' => ['required', 'string'], 'channel_key' => ['sometimes', 'nullable', 'string'], 'region' => ['sometimes', 'nullable', 'string']]);
        $binding = $query->binding($data['site_key'], $data['channel_key'] ?? null);
        $theme = $service->effectiveTheme($data['site_key'], $data['channel_key'] ?? null);

        return response()->json(['data' => ['theme_key' => $theme, 'binding' => ThemeBindingResource::make($binding), 'components' => array_map(ThemeComponentResource::make(...), $query->components($theme, $data['region'] ?? null))]], 200);
    }

    public function components(Request $request, ThemeIntegrationQuery $query): JsonResponse
    {
        $data = $request->validate(['theme_key' => ['required', 'string', 'max:100'], 'region' => ['sometimes', 'nullable', 'string', 'max:100']]);
        return response()->json(['data' => array_map(ThemeComponentResource::make(...), $query->components($data['theme_key'], $data['region'] ?? null))]);
    }

    public function registerComponent(Request $request, ThemeIntegrationService $service): JsonResponse
    {
        $data = $request->validate(['theme_key' => ['required', 'string', 'max:100'], 'region' => ['required', 'string', 'max:100'], 'component_key' => ['required', 'string', 'max:150'], 'view_contract' => ['sometimes', 'array'], 'configuration' => ['sometimes', 'array']]);
        $component = $service->registerComponent($data['theme_key'], $data['region'], $data['component_key'], $data['view_contract'] ?? [], $data['configuration'] ?? []);
        return response()->json(['data' => ThemeComponentResource::make($component)], 201);
    }
}
