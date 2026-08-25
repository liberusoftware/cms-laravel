<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceApi\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\Cms\ContentGovernance\Models\GovernanceRecord;
use Liberu\Cms\ContentGovernance\Services\ContentGovernanceService;

final class ContentGovernanceController
{
    public function index(Request $request, ContentGovernanceService $service): JsonResponse
    {
        return response()->json(['data' => $service->records($request->user()?->current_team_id, $request->integer('page_size', 25))]);
    }

    public function store(Request $request, ContentGovernanceService $service): JsonResponse
    {
        $data = $request->validate(['subject_type' => ['required', 'string', 'max:120'], 'subject_key' => ['required', 'string', 'max:180'], 'owner_id' => ['nullable', 'integer'], 'steward_id' => ['nullable', 'integer'], 'policy_labels' => ['array'], 'classification' => ['nullable', 'in:public,internal,confidential,restricted'], 'review_due_at' => ['nullable', 'date'], 'retention_until' => ['nullable', 'date']]);

        return response()->json(['data' => $service->record($data['subject_type'], $data['subject_key'], $data, $request->user()?->current_team_id)], 201);
    }

    public function hold(Request $request, GovernanceRecord $record, ContentGovernanceService $service): JsonResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        return response()->json(['data' => $service->placeLegalHold($record, $data['reason'])]);
    }

    public function evidence(Request $request, GovernanceRecord $record, ContentGovernanceService $service): JsonResponse
    {
        return response()->json(['data' => $service->addEvidence($record, $request->validate(['type' => ['required', 'string'], 'reference' => ['required', 'string'], 'notes' => ['nullable', 'string']]))], 201);
    }
}
