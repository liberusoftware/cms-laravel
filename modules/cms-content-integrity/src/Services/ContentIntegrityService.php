<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrity\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentIntegrity\Models\IntegrityFinding;
use Liberu\Cms\ContentIntegrity\Models\IntegrityScan;

final readonly class ContentIntegrityService
{
    public function findings(?int $teamId, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return IntegrityFinding::query()->where('team_id', $teamId)->when($status !== null, fn ($q) => $q->where('status', $status))->latest()->paginate(max(1, min($perPage, (int) config('content-integrity.pagination.max', 100))));
    }

    public function startScan(?int $teamId, string $scope = 'all'): IntegrityScan
    {
        if ($scope === '') {
            throw ValidationException::withMessages(['scope' => 'A scan scope is required.']);
        }

        return IntegrityScan::query()->create(['team_id' => $teamId, 'scope' => $scope, 'status' => 'running', 'started_at' => now()]);
    }

    public function finding(IntegrityScan $scan, array $data): IntegrityFinding
    {
        if (blank($data['subject_type'] ?? null) || blank($data['subject_key'] ?? null) || blank($data['kind'] ?? null) || blank($data['message'] ?? null)) {
            throw ValidationException::withMessages(['finding' => 'Finding subject, kind, and message are required.']);
        }
        $finding = IntegrityFinding::query()->create([...$data, 'scan_id' => $scan->id, 'team_id' => $scan->team_id]);
        $scan->increment('finding_count');

        return $finding;
    }

    public function completeScan(IntegrityScan $scan): IntegrityScan
    {
        $scan->update(['status' => 'completed', 'completed_at' => now()]);

        return $scan->fresh();
    }

    public function resolve(IntegrityFinding $finding): IntegrityFinding
    {
        $finding->update(['status' => 'resolved', 'resolved_at' => now()]);

        return $finding->fresh();
    }
}
