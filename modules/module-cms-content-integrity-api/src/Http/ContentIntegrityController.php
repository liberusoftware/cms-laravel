<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentIntegrity\Models\IntegrityFinding;
use Liberu\Cms\ContentIntegrity\Models\IntegrityScan;
use Liberu\Cms\ContentIntegrity\Services\ContentIntegrityService;

final class ContentIntegrityController
{
    public function index(Request $request, ContentIntegrityService $service): JsonResponse
    {
        return response()->json(['data' => $service->findings($request->user()?->current_team_id, $request->string('status')->toString() ?: null, $request->integer('page_size', 25))]);
    }

    public function scan(Request $request, ContentIntegrityService $service): JsonResponse
    {
        return response()->json(['data' => $service->startScan($request->user()?->current_team_id, $request->string('scope', 'all')->toString())], 201);
    }

    public function finding(Request $request, IntegrityScan $scan, ContentIntegrityService $service): JsonResponse
    {
        abort_unless($scan->team_id === $request->user()?->current_team_id, 404);
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'kind' => ['required', 'string'], 'severity' => ['nullable', 'string'], 'message' => ['required', 'string'], 'context' => ['array']]);

        return response()->json(['data' => $service->finding($scan, $this->normalized($data))], 201);
    }

    public function resolve(Request $request, IntegrityFinding $finding, ContentIntegrityService $service): JsonResponse
    {
        abort_unless($finding->team_id === $request->user()?->current_team_id, 404);

        return response()->json(['data' => $service->resolve($finding)]);
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
