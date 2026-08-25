<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ConfigurationManagement\Models\ConfigurationRelease;
use Liberu\Cms\ConfigurationManagement\Services\ConfigurationService;

final class ConfigurationController
{
    public function index(Request $request, ConfigurationService $service): JsonResponse
    {
        return response()->json(['data' => $service->releases($request->user()?->current_team_id, (string) $request->input('environment', 'production'), $request->integer('page_size', 25))]);
    }

    public function show(ConfigurationRelease $release): JsonResponse
    {
        return response()->json(['data' => $release]);
    }

    public function store(Request $request, ConfigurationService $service): JsonResponse
    {
        $data = $request->validate(['version' => ['required', 'string', 'max:80'], 'environment' => ['required', 'string', 'max:80'], 'payload' => ['required', 'array'], 'dependencies' => ['array']]);

        return response()->json(['data' => $service->export($data['payload'], $data['version'], $data['environment'], $request->user()?->getAuthIdentifier(), $request->user()?->current_team_id, $data['dependencies'] ?? [])], 201);
    }

    public function compare(ConfigurationRelease $release, ConfigurationRelease $other, ConfigurationService $service): JsonResponse
    {
        return response()->json(['data' => $service->compare($release, $other)]);
    }

    public function promote(Request $request, ConfigurationRelease $release, ConfigurationService $service): JsonResponse
    {
        $data = $request->validate(['available' => ['array']]);

        return response()->json(['data' => $service->promote($release, $data['available'] ?? [])]);
    }

    public function rollback(ConfigurationRelease $release, ConfigurationService $service): JsonResponse
    {
        return response()->json(['data' => $service->rollback($release)]);
    }
}
