<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentLocking\Models\ContentLock;
use Liberu\Cms\ContentLocking\Services\ContentLockingService;

final class ContentLockingController
{
    public function acquire(Request $request, ContentLockingService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'snapshot' => ['array'], 'ttl_minutes' => ['nullable', 'integer', 'min:1']]);

        return response()->json(['data' => $service->acquire($data['subject_type'], $data['subject_key'], $request->user()?->current_team_id, $request->user()?->getAuthIdentifier(), $data['snapshot'] ?? [], $data['ttl_minutes'] ?? null)], 201);
    }

    public function renew(Request $request, ContentLock $lock, ContentLockingService $service): JsonResponse
    {
        return response()->json(['data' => $service->renew($lock, $request->validate(['token' => ['required', 'string']])['token'])]);
    }

    public function compare(Request $request, ContentLock $lock, ContentLockingService $service): JsonResponse
    {
        return response()->json(['data' => $service->compare($lock, $request->validate(['current' => ['required', 'array']])['current'])]);
    }

    public function release(Request $request, ContentLock $lock, ContentLockingService $service): JsonResponse
    {
        $service->release($lock, $request->validate(['token' => ['required', 'string']])['token']);

        return response()->noContent();
    }
}
