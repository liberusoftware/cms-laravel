<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentAccess\Services\ContentAccessService;

final class ContentAccessController
{
    public function store(Request $request, ContentAccessService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string', 'max:120'], 'subject_key' => ['required', 'string', 'max:180'], 'visibility' => ['required', 'in:public,private,audience,scheduled'], 'audiences' => ['array'], 'fields' => ['array'], 'available_from' => ['nullable', 'date'], 'available_until' => ['nullable', 'date'], 'preview_allowed' => ['boolean']]);

        return response()->json(['data' => $service->rule($data['subject_type'], $data['subject_key'], $data, $request->user()?->current_team_id)], 201);
    }

    public function check(Request $request, ContentAccessService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'audiences' => ['array'], 'preview' => ['boolean'], 'private_token' => ['nullable', 'string']]);

        return response()->json(['allowed' => $service->canAccess($data['subject_type'], $data['subject_key'], $request->user()?->current_team_id, $data['audiences'] ?? [], $data['preview'] ?? false, $data['private_token'] ?? null)]);
    }

    public function privateLink(Request $request, ContentAccessService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'max_uses' => ['nullable', 'integer', 'min:1']]);

        return response()->json(['token' => $service->createPrivateLink($data['subject_type'], $data['subject_key'], $request->user()?->current_team_id, $data['max_uses'] ?? null)], 201);
    }
}
