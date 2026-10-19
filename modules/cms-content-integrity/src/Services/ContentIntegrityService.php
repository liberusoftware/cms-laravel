<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrity\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentIntegrity\Models\IntegrityFinding;
use Liberu\Cms\ContentIntegrity\Models\IntegrityScan;

final readonly class ContentIntegrityService
{
    /** @return LengthAwarePaginator<int, IntegrityFinding> */
    public function findings(?int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        $maximum = config('content-integrity.pagination.max', 100);

        return IntegrityFinding::query()->where('team_id', $teamId)->when($status !== null, fn ($q) => $q->where('status', $status))->latest()->paginate(max(1, min($perPage, is_int($maximum) ? $maximum : 100)));
    }

    public function startScan(?int $teamId, string $scope = 'all'): IntegrityScan
    {
        if (trim($scope) === '') {
            throw ValidationException::withMessages(['scope' => 'A scan scope is required.']);
        }

        return IntegrityScan::query()->create(['team_id' => $teamId, 'scope' => $scope, 'status' => 'running', 'started_at' => now()]);
    }

    /** @param array<string, mixed> $data */
    public function finding(IntegrityScan $scan, array $data): IntegrityFinding
    {
        if ($scan->status !== 'running') {
            throw ValidationException::withMessages(['scan' => 'Findings can only be added to a running scan.']);
        }
        if (blank($data['subject_type'] ?? null) || blank($data['subject_key'] ?? null) || blank($data['kind'] ?? null) || blank($data['message'] ?? null)) {
            throw ValidationException::withMessages(['finding' => 'Finding subject, kind, and message are required.']);
        }
        $finding = IntegrityFinding::query()->create([...$data, 'scan_id' => $scan->id, 'team_id' => $scan->team_id]);
        $scan->increment('finding_count');

        return $finding;
    }

    public function completeScan(IntegrityScan $scan): IntegrityScan
    {
        if ($scan->status !== 'running') {
            throw ValidationException::withMessages(['scan' => 'Only a running scan can be completed.']);
        }
        $scan->update(['status' => 'completed', 'completed_at' => now()]);

        $fresh = $scan->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The integrity scan could not be refreshed.');
        }

        return $fresh;
    }

    public function resolve(IntegrityFinding $finding): IntegrityFinding
    {
        $finding->update(['status' => 'resolved', 'resolved_at' => now()]);

        $fresh = $finding->fresh();
        if (! $fresh) {
            throw new \RuntimeException('The integrity finding could not be refreshed.');
        }

        return $fresh;
    }
}
