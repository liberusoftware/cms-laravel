<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\IntegrationDirectory\Models\Integration;
use Liberu\Cms\IntegrationDirectory\Services\IntegrationDirectoryService;

final class IntegrationDirectoryController
{
    public function index(Request $request, IntegrationDirectoryService $service): JsonResponse
    {
        return response()->json(['data' => $service->enabled($request->string('category')->toString())]);
    }

    public function store(Request $request, IntegrationDirectoryService $service): JsonResponse
    {
        $data = $request->validate(['key' => ['required', 'string', 'max:80'], 'name' => ['required', 'string', 'max:180'], 'provider' => ['required', 'string', 'max:120'], 'configuration' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->register($data['key'], $data['name'], $data['provider'], $data['configuration'] ?? [])], 201);
    }

    public function health(string $integration, Request $request, IntegrationDirectoryService $service): JsonResponse
    {
        $data = $request->validate(['status' => ['required', 'in:healthy,degraded,unhealthy,unknown'], 'message' => ['sometimes', 'nullable', 'string', 'max:2000']]);

        return response()->json(['data' => $service->health(Integration::query()->findOrFail($integration), $data['status'], $data['message'] ?? null)]);
    }
}
