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

        return response()->json(['data' => $service->createArtifact($request->user()?->current_team_id, $this->normalized($data))], 201);
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

        return response()->json(['data' => $service->schedule($request->user()?->current_team_id, $this->normalized($data))], 201);
    }

    /** @return array<string, mixed> */
    private function normalized(mixed $value): array
    {
        $data = [];
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                if (is_string($key)) {
                    $data[$key] = $item;
                }
            }
        }

        return $data;
    }
}
