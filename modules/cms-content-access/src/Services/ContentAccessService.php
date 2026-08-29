<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccess\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentAccess\Models\AccessRule;
use Liberu\Cms\ContentAccess\Models\PrivateLink;

final readonly class ContentAccessService
{
    public function rule(string $subjectType, string $subjectKey, array $data = [], ?int $teamId = null): AccessRule
    {
        $visibility = $data['visibility'] ?? 'public';
        if (! in_array($visibility, ['public', 'private', 'audience', 'scheduled'], true)) {
            throw ValidationException::withMessages(['visibility' => 'The visibility mode is invalid.']);
        }
        if (($data['available_from'] ?? null) !== null && ($data['available_until'] ?? null) !== null && $data['available_from'] >= $data['available_until']) {
            throw ValidationException::withMessages(['available_until' => 'The access window is invalid.']);
        }

        return AccessRule::query()->updateOrCreate(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey], [...$data, 'team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey]);
    }

    public function canAccess(string $subjectType, string $subjectKey, ?int $teamId, array $audiences = [], bool $preview = false, ?string $privateToken = null): bool
    {
        if ($privateToken !== null && $this->consumePrivateLink($privateToken, $subjectType, $subjectKey, $teamId)) {
            return true;
        }
        $rule = AccessRule::query()->where(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey])->first();
        if ($privateToken !== null) {
            return false;
        }
        if (! $rule) {
            return true;
        }
        if ($preview && $rule->preview_allowed) {
            return true;
        }
        if ($rule->visibility === 'private') {
            return false;
        }
        if ($rule->visibility === 'audience' && array_intersect($rule->audiences ?? [], $audiences) === []) {
            return false;
        }

        return $rule->visibility !== 'scheduled' || (($rule->available_from === null || $rule->available_from->isPast()) && ($rule->available_until === null || $rule->available_until->isFuture()));
    }

    public function createPrivateLink(string $subjectType, string $subjectKey, ?int $teamId, ?int $maxUses = null): string
    {
        if ($maxUses !== null && $maxUses < 1) {
            throw ValidationException::withMessages(['max_uses' => 'Maximum uses must be at least one.']);
        }
        $token = Str::random(64);
        PrivateLink::query()->create(['team_id' => $teamId, 'token_hash' => hash('sha256', $token), 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'expires_at' => now()->addMinutes((int) config('content-access.private_link_ttl_minutes', 60)), 'max_uses' => $maxUses]);

        return $token;
    }

    public function consumePrivateLink(string $token, string $subjectType, string $subjectKey, ?int $teamId): bool
    {
        return DB::transaction(function () use ($token, $subjectType, $subjectKey, $teamId): bool {
            $link = PrivateLink::query()->where(['token_hash' => hash('sha256', $token), 'team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey])->lockForUpdate()->first();
            if (! $link || $link->revoked_at !== null || $link->expires_at->isPast() || ($link->max_uses !== null && $link->uses >= $link->max_uses)) {
                return false;
            }
            $link->increment('uses');

            return true;
        });
    }
}
