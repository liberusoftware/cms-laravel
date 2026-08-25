<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernance\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentGovernance\Models\GovernanceRecord;

final readonly class ContentGovernanceService
{
    public function records(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        return GovernanceRecord::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, (int) config('content-governance.pagination.max', 100))));
    }

    public function record(string $subjectType, string $subjectKey, array $data = [], ?int $teamId = null): GovernanceRecord
    {
        if ($subjectType === '' || $subjectKey === '') {
            throw ValidationException::withMessages(['subject' => 'A subject type and key are required.']);
        }
        $classification = $data['classification'] ?? 'internal';
        if (! in_array($classification, ['public', 'internal', 'confidential', 'restricted'], true)) {
            throw ValidationException::withMessages(['classification' => 'The classification is invalid.']);
        }

        return GovernanceRecord::query()->updateOrCreate(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey], [...$data, 'team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'classification' => $classification]);
    }

    public function placeLegalHold(GovernanceRecord $record, string $reason): GovernanceRecord
    {
        if (blank($reason)) {
            throw ValidationException::withMessages(['reason' => 'A legal hold reason is required.']);
        }
        $record->update(['legal_hold' => true, 'legal_hold_at' => now(), 'legal_hold_reason' => $reason]);

        return $record->fresh();
    }

    public function releaseLegalHold(GovernanceRecord $record): GovernanceRecord
    {
        $record->update(['legal_hold' => false, 'legal_hold_reason' => null]);

        return $record->fresh();
    }

    public function addEvidence(GovernanceRecord $record, array $evidence): GovernanceRecord
    {
        if (blank($evidence['type'] ?? null) || blank($evidence['reference'] ?? null)) {
            throw ValidationException::withMessages(['evidence' => 'Evidence type and reference are required.']);
        }
        $record->update(['evidence' => [...($record->evidence ?? []), [...$evidence, 'recorded_at' => now()->toIso8601String()]]]);

        return $record->fresh();
    }
}
