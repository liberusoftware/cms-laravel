<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\Cms\ContentLocking\Models\ContentLock;
use Liberu\Cms\ContentLocking\Services\ContentLockingService;

final class ContentLockingController
{
    public function acquire(Request $request, ContentLockingService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'snapshot' => ['array'], 'ttl_minutes' => ['nullable', 'integer', 'min:1']]));

        $subjectType = is_string($data['subject_type'] ?? null) ? $data['subject_type'] : '';
        $subjectKey = is_string($data['subject_key'] ?? null) ? $data['subject_key'] : '';
        $holderId = $request->user()?->getAuthIdentifier();
        $holderId = is_int($holderId) ? $holderId : null;
        $ttl = is_int($data['ttl_minutes'] ?? null) ? $data['ttl_minutes'] : null;

        return response()->json(['data' => $service->acquire($subjectType, $subjectKey, $request->user()?->current_team_id, $holderId, $this->normalized($data['snapshot'] ?? []), $ttl)], 201);
    }

    public function renew(Request $request, ContentLock $lock, ContentLockingService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['token' => ['required', 'string']]));
        $token = is_string($data['token'] ?? null) ? $data['token'] : '';

        return response()->json(['data' => $service->renew($lock, $token)]);
    }

    public function compare(Request $request, ContentLock $lock, ContentLockingService $service): JsonResponse
    {
        $data = $this->normalized($request->validate(['current' => ['required', 'array']]));

        return response()->json(['data' => $service->compare($lock, $this->normalized($data['current'] ?? []))]);
    }

    public function release(Request $request, ContentLock $lock, ContentLockingService $service): Response
    {
        $data = $this->normalized($request->validate(['token' => ['required', 'string']]));
        $token = is_string($data['token'] ?? null) ? $data['token'] : '';
        $service->release($lock, $token);

        return response()->noContent();
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
