<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligence\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentIntelligence\Models\ContentInsight;

final readonly class ContentIntelligenceService
{
    public function insights(?int $teamId, ?string $metric = null, ?string $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return ContentInsight::query()->where('team_id', $teamId)->when($metric !== null, fn ($q) => $q->where('metric', $metric))->when($status !== null, fn ($q) => $q->where('status', $status))->latest()->paginate(max(1, min($perPage, (int) config('content-intelligence.pagination.max', 100))));
    }

    public function analyze(array $data, ?int $teamId = null): ContentInsight
    {
        if (blank($data['subject_type'] ?? null) || blank($data['subject_key'] ?? null) || blank($data['metric'] ?? null) || blank($data['summary'] ?? null)) {
            throw ValidationException::withMessages(['insight' => 'Subject, metric, and summary are required.']);
        }
        if (isset($data['score']) && ($data['score'] < 0 || $data['score'] > 100)) {
            throw ValidationException::withMessages(['score' => 'Score must be between 0 and 100.']);
        }

        return ContentInsight::query()->create([
            ...array_intersect_key($data, array_flip(['subject_type', 'subject_key', 'metric', 'score', 'severity', 'summary', 'rationale', 'context'])),
            'team_id' => $teamId,
            'status' => 'open',
        ]);
    }

    public function review(ContentInsight $insight, string $status): ContentInsight
    {
        if (! in_array($status, ['accepted', 'dismissed', 'queued'], true)) {
            throw ValidationException::withMessages(['status' => 'The review status is invalid.']);
        }
        if (! in_array($insight->status, ['open', 'queued'], true)) {
            throw ValidationException::withMessages(['status' => 'Only open or queued insights can be reviewed.']);
        }
        $insight->update(['status' => $status, 'reviewed_at' => now()]);

        return $insight->fresh();
    }
}
