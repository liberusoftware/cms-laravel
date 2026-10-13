<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentIntelligence\Models\ContentInsight;
use Liberu\Cms\ContentIntelligence\Services\ContentIntelligenceService;

final class ContentIntelligenceController
{
    public function index(Request $request, ContentIntelligenceService $service): JsonResponse
    {
        return response()->json(['data' => $service->insights($request->user()?->current_team_id, $request->string('metric')->toString() ?: null, $request->string('status')->toString() ?: null, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, ContentIntelligenceService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'metric' => ['required', 'string'], 'score' => ['nullable', 'numeric', 'between:0,100'], 'severity' => ['nullable', 'string'], 'summary' => ['required', 'string'], 'rationale' => ['nullable', 'string'], 'context' => ['array']]);

        return response()->json(['data' => $service->analyze($this->normalized($data), $request->user()?->current_team_id)], 201);
    }

    public function review(Request $request, ContentInsight $insight, ContentIntelligenceService $service): JsonResponse
    {
        abort_unless($insight->team_id === $request->user()?->current_team_id, 404);
        $data = $request->validate(['status' => ['required', 'in:accepted,dismissed,queued']]);
        $data = is_array($data) ? $data : [];

        return response()->json(['data' => $service->review($insight, is_string($data['status'] ?? null) ? $data['status'] : '')]);
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
