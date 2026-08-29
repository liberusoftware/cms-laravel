<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFrameworkApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\MigrationFramework\Models\MigrationJob;
use Liberu\Cms\MigrationFramework\Services\MigrationFrameworkService;

final class MigrationFrameworkController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => MigrationJob::query()->latest()->paginate(max(1, min(100, $request->integer('per_page', 15))))]);
    }

    public function start(Request $request, MigrationFrameworkService $service): JsonResponse
    {
        $data = $request->validate(['source' => ['required', 'string', 'max:80'], 'options' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->start($data['source'], $data['options'] ?? [])], 201);
    }

    private function job(string $publicId): MigrationJob
    {
        return MigrationJob::query()->where('public_id', $publicId)->firstOrFail();
    }

    public function add(string $publicId, Request $request, MigrationFrameworkService $service): JsonResponse
    {
        $data = $request->validate(['record_type' => ['required', 'string', 'max:80'], 'source_id' => ['required', 'string', 'max:255'], 'payload' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->add($this->job($publicId), $data['record_type'], $data['source_id'], $data['payload'] ?? [])], 201);
    }

    public function process(string $publicId, int|string $record, Request $request, MigrationFrameworkService $service): JsonResponse
    {
        $data = $request->validate(['success' => ['required', 'boolean'], 'failure_reason' => ['sometimes', 'nullable', 'string']]);
        $model = $this->job($publicId)->records()->findOrFail($record);

        return response()->json(['data' => $service->process($model, (bool) $data['success'], $data['failure_reason'] ?? null)]);
    }

    public function complete(string $publicId, MigrationFrameworkService $service): JsonResponse
    {
        return response()->json(['data' => $service->complete($this->job($publicId))]);
    }
}
