<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperations\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\SecurityOperations\Models\SecurityOperation;

final class SecurityOperationsService
{
    public function inventory(array $packages, ?int $actorId = null): SecurityOperation
    {
        return $this->record('update-inventory', 'packages', 'complete', ['packages' => $packages], null, $actorId);
    }

    public function provenance(string $extension, string $version, string $source, ?int $actorId = null): SecurityOperation
    {
        if (! filter_var($source, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages(['source' => 'Extension provenance requires a valid source URL.']);
        }

return $this->record('extension-provenance', $extension, 'verified', compact('version', 'source'), null, $actorId);
    }

    public function integrity(string $subject, string $content, ?int $actorId = null): SecurityOperation
    {
        return $this->record('content-integrity', $subject, 'verified', ['algorithm' => 'sha256'], hash('sha256', $content), $actorId);
    }

    public function scan(UploadedFile $file, ?int $actorId = null): SecurityOperation
    {
        $blocked = ['php', 'phar', 'exe', 'js', 'sh'];
        $extension = strtolower($file->getClientOriginalExtension());
        $status = in_array($extension, $blocked, true) ? 'quarantined' : 'clean';

        return $this->record('upload-scan', $file->getClientOriginalName(), $status, ['extension' => $extension, 'mime' => $file->getMimeType(), 'size' => $file->getSize()], null, $actorId);
    }

    public function advisory(string $title, string $severity, array $details = [], ?int $actorId = null): SecurityOperation
    {
        if (! in_array($severity, ['low', 'medium', 'high', 'critical'], true)) {
            throw ValidationException::withMessages(['severity' => 'Unsupported advisory severity.']);
        }

return $this->record('advisory', $title, $severity, $details, null, $actorId);
    }

    /** @return array<int, string> */
    public function incidentBundle(string $incident, ?int $actorId = null): array
    {
        return ['incident' => $incident, 'generated_at' => now()->toIso8601String(), 'operations' => SecurityOperation::query()->latest()->limit(100)->get()->map(fn (SecurityOperation $operation): array => ['kind' => $operation->kind, 'status' => $operation->status, 'subject' => $operation->subject])->all()];
    }

    private function record(string $kind, ?string $subject, string $status, array $evidence, ?string $hash, ?int $actorId): SecurityOperation
    {
        return SecurityOperation::query()->create(compact('kind', 'subject', 'status', 'evidence', 'hash', 'actorId') + ['content_hash' => $hash, 'actor_id' => $actorId]);
    }
}
