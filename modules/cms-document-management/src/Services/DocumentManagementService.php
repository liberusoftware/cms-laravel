<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DocumentManagement\Models\Document;

final readonly class DocumentManagementService
{
    /** @return LengthAwarePaginator<int, Document> */
    public function documents(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        $configuredMax = config('document-management.pagination.max', 100);
        $maxPerPage = is_int($configuredMax) ? $configuredMax : 100;

        return Document::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, $maxPerPage)));
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, ?int $teamId = null): Document
    {
        if (blank($data['title'] ?? null) || blank($data['slug'] ?? null)) {
            throw ValidationException::withMessages(['document' => 'Title and slug are required.']);
        }
        if (! in_array($data['status'] ?? 'draft', ['draft', 'processing', 'ready', 'archived', 'failed'], true)) {
            throw ValidationException::withMessages(['status' => 'The document status is invalid.']);
        }

        $configuredDays = config('document-management.retention.default_days', 3650);
        $retentionDays = is_int($configuredDays) ? $configuredDays : 3650;

        return Document::query()->create([...$data, 'team_id' => $teamId, 'status' => $data['status'] ?? 'draft', 'retention_until' => $data['retention_until'] ?? now()->addDays($retentionDays)]);
    }

    public function transition(Document $document, string $status): Document
    {
        if (! in_array($status, ['draft', 'processing', 'ready', 'archived', 'failed'], true)) {
            throw ValidationException::withMessages(['status' => 'The document status is invalid.']);
        }
        $document->update(['status' => $status]);

        return $document->refresh();
    }

    public function recordDownload(Document $document, ?int $userId = null, ?string $ip = null): void
    {
        $document->downloads()->create(['user_id' => $userId, 'ip_address' => $ip, 'downloaded_at' => now()]);
    }
}
