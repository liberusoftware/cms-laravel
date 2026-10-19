<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernance\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentGovernance\Models\GovernanceRecord;

final readonly class ContentGovernanceService
{
    /** @return LengthAwarePaginator<int, GovernanceRecord> */
    public function records(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        $maximum = config('content-governance.pagination.max', 100);

        return GovernanceRecord::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, is_int($maximum) ? $maximum : 100)));
    }

    /** @param array<string, mixed> $data */
    public function record(string $subjectType, string $subjectKey, array $data = [], ?int $teamId = null): GovernanceRecord
    {
        if ($subjectType === '' || $subjectKey === '') {
            throw ValidationException::withMessages(['subject' => 'A subject type and key are required.']);
        }
        $classification = $data['classification'] ?? 'internal';
        if (! in_array($classification, ['public', 'internal', 'confidential', 'restricted'], true)) {
            throw ValidationException::withMessages(['classification' => 'The classification is invalid.']);
        }

        return GovernanceRecord::query()->updateOrCreate(
            ['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey],
            [...array_intersect_key($data, array_flip(['owner_id', 'steward_id', 'policy_labels', 'review_due_at', 'retention_until', 'evidence'])), 'classification' => $classification, 'legal_hold' => false, 'legal_hold_at' => null, 'legal_hold_reason' => null],
        );
    }

    public function placeLegalHold(GovernanceRecord $record, string $reason): GovernanceRecord
    {
        if (blank($reason)) {
            throw ValidationException::withMessages(['reason' => 'A legal hold reason is required.']);
        }
        $record->update(['legal_hold' => true, 'legal_hold_at' => now(), 'legal_hold_reason' => $reason]);

        $fresh = $record->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The governance record could not be refreshed.');
        }

        return $fresh;
    }

    public function releaseLegalHold(GovernanceRecord $record): GovernanceRecord
    {
        $record->update(['legal_hold' => false, 'legal_hold_at' => null, 'legal_hold_reason' => null]);

        $fresh = $record->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The governance record could not be refreshed.');
        }

        return $fresh;
    }

    /** @param array<string, mixed> $evidence */
    public function addEvidence(GovernanceRecord $record, array $evidence): GovernanceRecord
    {
        if (blank($evidence['type'] ?? null) || blank($evidence['reference'] ?? null)) {
            throw ValidationException::withMessages(['evidence' => 'Evidence type and reference are required.']);
        }
        $existing = is_array($record->evidence) ? $record->evidence : [];
        $record->update(['evidence' => [...$existing, [...$evidence, 'recorded_at' => now()->toIso8601String()]]]);

        $fresh = $record->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The governance record could not be refreshed.');
        }

        return $fresh;
    }
}
