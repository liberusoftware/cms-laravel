<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\BackupAndRestore\Models\BackupArtifact;
use Liberu\Cms\BackupAndRestore\Services\BackupAndRestoreService;

final class BackupAndRestoreController
{
    public function create(Request $request, BackupAndRestoreService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:180'], 'artifact_type' => ['required', 'string'], 'path' => ['required', 'string'], 'disk' => ['sometimes', 'string'], 'checksum' => ['sometimes', 'string'], 'encrypted' => ['sometimes', 'boolean'], 'metadata' => ['sometimes', 'array']]);

        return response()->json(['data' => $service->createArtifact(null, $data)], 201);
    }

    public function verify(BackupArtifact $artifact, BackupAndRestoreService $service): JsonResponse
    {
        return response()->json(['data' => $service->verify($artifact)]);
    }

    public function preview(BackupArtifact $artifact, BackupAndRestoreService $service): JsonResponse
    {
        return response()->json(['data' => $service->restorePreview($artifact)]);
    }

    public function schedule(Request $request, BackupAndRestoreService $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:180'], 'frequency' => ['required', 'string'], 'artifact_types' => ['sometimes', 'array'], 'retention_days' => ['sometimes', 'integer'], 'enabled' => ['sometimes', 'boolean']]);

        return response()->json(['data' => $service->schedule(null, $data)], 201);
    }
}
