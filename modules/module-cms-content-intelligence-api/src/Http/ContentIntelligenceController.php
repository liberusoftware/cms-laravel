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
        return response()->json(['data' => $service->insights($request->user()?->current_team_id, $request->input('metric'), $request->input('status'), $request->integer('page_size', 25))]);
    }

    public function store(Request $request, ContentIntelligenceService $service): JsonResponse
    {
        return response()->json(['data' => $service->analyze($request->validate(['subject_type' => ['required', 'string'], 'subject_key' => ['required', 'string'], 'metric' => ['required', 'string'], 'score' => ['nullable', 'numeric', 'between:0,100'], 'severity' => ['nullable', 'string'], 'summary' => ['required', 'string'], 'rationale' => ['nullable', 'string'], 'context' => ['array']]), $request->user()?->current_team_id)], 201);
    }

    public function review(Request $request, ContentInsight $insight, ContentIntelligenceService $service): JsonResponse
    {
        return response()->json(['data' => $service->review($insight, $request->validate(['status' => ['required', 'in:accepted,dismissed,queued']])['status'])]);
    }
}
