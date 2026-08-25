<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagement\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DocumentManagement\Models\Document;

final readonly class DocumentManagementService
{
    public function documents(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        return Document::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, (int) config('document-management.pagination.max', 100))));
    }

    public function create(array $data, ?int $teamId = null): Document
    {
        if (blank($data['title'] ?? null) || blank($data['slug'] ?? null)) {
            throw ValidationException::withMessages(['document' => 'Title and slug are required.']);
        }
        if (! in_array($data['status'] ?? 'draft', ['draft', 'processing', 'ready', 'archived', 'failed'], true)) {
            throw ValidationException::withMessages(['status' => 'The document status is invalid.']);
        }

        return Document::query()->create([...$data, 'team_id' => $teamId, 'status' => $data['status'] ?? 'draft', 'retention_until' => $data['retention_until'] ?? now()->addDays((int) config('document-management.retention.default_days', 3650))]);
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
