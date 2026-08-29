<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigrationApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\JoomlaMigration\Services\JoomlaMigrationService;
use Liberu\Cms\MigrationFramework\Models\MigrationJob;

final class JoomlaMigrationController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => MigrationJob::query()->where('source', 'joomla')->latest()->paginate(max(1, min(100, $request->integer('per_page', 15))))]);
    }

    public function start(Request $request, JoomlaMigrationService $service): JsonResponse
    {
        $data = $request->validate(['source' => ['required', 'string', 'max:2048'], 'options' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->start($data['source'], $data['options'] ?? [])], 201);
    }

    private function job(string $publicId): MigrationJob
    {
        return MigrationJob::query()->where('public_id', $publicId)->where('source', 'joomla')->firstOrFail();
    }

    public function add(string $publicId, Request $request, JoomlaMigrationService $service): JsonResponse
    {
        $data = $request->validate(['record_type' => ['required', 'string', 'max:80'], 'source_id' => ['required', 'string', 'max:255'], 'payload' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->add($this->job($publicId), $data['record_type'], $data['source_id'], $data['payload'] ?? [])], 201);
    }

    public function process(string $publicId, int|string $record, Request $request, JoomlaMigrationService $service): JsonResponse
    {
        $data = $request->validate(['success' => ['required', 'boolean'], 'failure_reason' => ['sometimes', 'nullable', 'string']]);

        return response()->json(['data' => $service->process($this->job($publicId)->records()->findOrFail($record), (bool) $data['success'], $data['failure_reason'] ?? null)]);
    }

    public function complete(string $publicId, JoomlaMigrationService $service): JsonResponse
    {
        return response()->json(['data' => $service->complete($this->job($publicId))]);
    }
}
