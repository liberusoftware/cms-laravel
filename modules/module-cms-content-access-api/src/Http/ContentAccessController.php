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
        $raw = $request->validate(['subject_type' => ['required', 'string', 'max:120'], 'subject_key' => ['required', 'string', 'max:180'], 'visibility' => ['required', 'in:public,private,audience,scheduled'], 'audiences' => ['array'], 'fields' => ['array'], 'available_from' => ['nullable', 'date'], 'available_until' => ['nullable', 'date'], 'preview_allowed' => ['boolean']]);
        $data = $this->normalized($raw);

        return response()->json(['data' => $service->rule(is_string($data['subject_type'] ?? null) ? $data['subject_type'] : '', is_string($data['subject_key'] ?? null) ? $data['subject_key'] : '', $data, $request->user()?->current_team_id)], 201);
    }

    public function check(Request $request, ContentAccessService $service): JsonResponse
    {
        $raw = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'audiences' => ['array'], 'preview' => ['boolean'], 'private_token' => ['nullable', 'string']]);
        $data = is_array($raw) ? $raw : [];

        $audiences = is_array($data['audiences'] ?? null) ? array_values(array_filter($data['audiences'], static fn (mixed $item): bool => is_string($item))) : [];

        return response()->json(['allowed' => $service->canAccess(is_string($data['subject_type'] ?? null) ? $data['subject_type'] : '', is_string($data['subject_key'] ?? null) ? $data['subject_key'] : '', $request->user()?->current_team_id, $audiences, is_bool($data['preview'] ?? null) ? $data['preview'] : false, is_string($data['private_token'] ?? null) ? $data['private_token'] : null)]);
    }

    public function privateLink(Request $request, ContentAccessService $service): JsonResponse
    {
        $raw = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'max_uses' => ['nullable', 'integer', 'min:1']]);
        $data = is_array($raw) ? $raw : [];

        return response()->json(['token' => $service->createPrivateLink(is_string($data['subject_type'] ?? null) ? $data['subject_type'] : '', is_string($data['subject_key'] ?? null) ? $data['subject_key'] : '', $request->user()?->current_team_id, is_int($data['max_uses'] ?? null) ? $data['max_uses'] : null)], 201);
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
