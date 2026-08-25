<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLocking\Services;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentLocking\Models\ContentLock;

final readonly class ContentLockingService
{
    public function acquire(string $subjectType, string $subjectKey, ?int $teamId, ?int $holderId, array $snapshot = [], ?int $ttlMinutes = null): ContentLock
    {
        $ttl = $ttlMinutes ?? (int) config('content-locking.default_ttl_minutes', 15);
        if ($subjectType === '' || $subjectKey === '') {
            throw ValidationException::withMessages(['subject' => 'A subject type and key are required.']);
        }
        if ($ttl < 1 || $ttl > (int) config('content-locking.max_ttl_minutes', 120)) {
            throw ValidationException::withMessages(['ttl' => 'The lock duration is invalid.']);
        }
        $existing = ContentLock::query()->where(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey])->first();
        if ($existing?->expires_at->isFuture() && $existing->holder_id !== $holderId) {
            throw ValidationException::withMessages(['lock' => 'Content is currently locked by another editor.']);
        }

        return ContentLock::query()->updateOrCreate(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey], ['holder_id' => $holderId, 'token' => $existing?->token ?? Str::random(64), 'version' => $existing?->version ?? 1, 'snapshot' => $snapshot, 'expires_at' => now()->addMinutes($ttl)]);
    }

    public function renew(ContentLock $lock, string $token, ?int $ttlMinutes = null): ContentLock
    {
        $this->assertToken($lock, $token);
        $ttl = $ttlMinutes ?? (int) config('content-locking.default_ttl_minutes', 15);
        $lock->update(['expires_at' => now()->addMinutes($ttl)]);

        return $lock->fresh();
    }

    public function release(ContentLock $lock, string $token): void
    {
        $this->assertToken($lock, $token);
        $lock->delete();
    }

    public function compare(ContentLock $lock, array $current): array
    {
        $changes = [];
        foreach (array_unique([...array_keys($lock->snapshot ?? []), ...array_keys($current)]) as $key) {
            if (($lock->snapshot[$key] ?? null) !== ($current[$key] ?? null)) {
                $changes[] = ['path' => (string) $key, 'expected' => $lock->snapshot[$key] ?? null, 'actual' => $current[$key] ?? null];
            }
        }

        return ['conflicted' => $changes !== [], 'changes' => $changes];
    }

    public function merge(ContentLock $lock, array $current, string $token): array
    {
        $this->assertToken($lock, $token);
        $comparison = $this->compare($lock, $current);
        if ($comparison['conflicted']) {
            throw ValidationException::withMessages(['conflict' => 'The content changed while it was locked.']);
        }

        return $current;
    }

    private function assertToken(ContentLock $lock, string $token): void
    {
        if (! hash_equals($lock->token, $token) || $lock->expires_at->isPast()) {
            throw ValidationException::withMessages(['lock' => 'The lock token is invalid or expired.']);
        }
    }
}
