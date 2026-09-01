<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContent\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\MembershipContent\Models\AccessRule;
use Liberu\Cms\MembershipContent\Models\Download;
use Liberu\Cms\MembershipContent\Models\DripSchedule;
use Liberu\Cms\MembershipContent\Models\Entitlement;
use Liberu\Cms\MembershipContent\Models\MembershipContent;
use Liberu\Cms\MembershipContent\Models\PortalIntegration;

final class MembershipContentService
{
    /** @return LengthAwarePaginator<int, MembershipContent> */
    public function list(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        return MembershipContent::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, 100)));
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, ?int $teamId = null): MembershipContent
    {
        $this->validateContent($data);

        return MembershipContent::query()->create([
            ...$data,
            'team_id' => $teamId,
            'public_id' => (string) Str::uuid(),
            'status' => $data['status'] ?? 'draft',
        ]);
    }

    /** @param array<string, mixed> $data */
    public function update(MembershipContent $content, array $data): MembershipContent
    {
        $this->validateContent([...$content->only(['title', 'subject_type', 'subject_key', 'status', 'available_from', 'available_until']), ...$data]);
        $content->update($data);

        return $content->refresh();
    }

    public function rule(MembershipContent $content, string $entitlementKey, ?int $minimumDays = null): AccessRule
    {
        $this->key($entitlementKey, 'entitlement_key');
        if ($minimumDays !== null && $minimumDays < 0) {
            throw ValidationException::withMessages(['minimum_days' => 'Minimum days cannot be negative.']);
        }

        return $content->accessRules()->updateOrCreate(['entitlement_key' => $entitlementKey], ['minimum_days' => $minimumDays]);
    }

    public function grantEntitlement(string $subjectType, string $subjectKey, string $entitlementKey, ?int $teamId = null, ?string $startsAt = null, ?string $expiresAt = null, ?string $externalId = null): Entitlement
    {
        $this->key($subjectType, 'subject_type');
        $this->key($subjectKey, 'subject_key');
        $this->key($entitlementKey, 'entitlement_key');
        $starts = $this->date($startsAt, 'starts_at');
        $expires = $this->date($expiresAt, 'expires_at');
        if ($starts instanceof Carbon && $expires instanceof Carbon && $expires->lessThanOrEqualTo($starts)) {
            throw ValidationException::withMessages(['expires_at' => 'An entitlement must expire after it starts.']);
        }

        return Entitlement::query()->updateOrCreate(
            ['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'entitlement_key' => $entitlementKey],
            ['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'entitlement_key' => $entitlementKey, 'external_id' => $externalId, 'starts_at' => $starts, 'expires_at' => $expires],
        );
    }

    public function revokeEntitlement(string $subjectType, string $subjectKey, string $entitlementKey, ?int $teamId = null): bool
    {
        return (bool) Entitlement::query()->where(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey, 'entitlement_key' => $entitlementKey])->delete();
    }

    public function drip(MembershipContent $content, string $entitlementKey, int $delayDays): DripSchedule
    {
        $this->key($entitlementKey, 'entitlement_key');
        if ($delayDays < 0 || $delayDays > 3650) {
            throw ValidationException::withMessages(['delay_days' => 'Drip delay must be between zero and 3650 days.']);
        }

        return $content->dripSchedules()->updateOrCreate(['entitlement_key' => $entitlementKey], ['delay_days' => $delayDays]);
    }

    /** @param array<string, mixed> $data */
    public function download(MembershipContent $content, array $data): Download
    {
        $path = $data['path'] ?? null;
        $filename = $data['filename'] ?? null;
        if (! is_string($path) || ! is_string($filename)) {
            throw ValidationException::withMessages(['path' => 'A valid path and filename are required.']);
        }
        $this->key($path, 'path');
        $this->key($filename, 'filename');
        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            throw ValidationException::withMessages(['path' => 'Download paths must stay inside the configured storage disk.']);
        }

        return $content->downloads()->create([...$data, 'public_id' => (string) Str::uuid()]);
    }

    /** @param array<string, mixed> $metadata */
    public function portal(string $provider, string $externalId, ?int $teamId = null, array $metadata = []): PortalIntegration
    {
        $this->key($provider, 'provider');
        $this->key($externalId, 'external_id');

        return PortalIntegration::query()->updateOrCreate(
            ['team_id' => $teamId, 'provider' => $provider, 'external_id' => $externalId],
            ['status' => 'active', 'metadata' => $metadata],
        );
    }

    public function canAccess(MembershipContent $content, string $subjectType, string $subjectKey, ?int $teamId = null, ?Carbon $at = null): bool
    {
        if ($content->team_id !== $teamId || ! $content->isPublished()) {
            return false;
        }
        $now = $at ?? now();
        if (($content->available_from !== null && $content->available_from->isAfter($now)) || ($content->available_until !== null && $content->available_until->isBefore($now))) {
            return false;
        }
        $rules = $content->accessRules;
        if ($rules->isEmpty()) {
            return true;
        }

        $entitlements = Entitlement::query()->where(['team_id' => $teamId, 'subject_type' => $subjectType, 'subject_key' => $subjectKey])->get()->keyBy('entitlement_key');
        foreach ($rules as $rule) {
            $entitlement = $entitlements->get($rule->entitlement_key);
            if (! $entitlement || ($entitlement->starts_at !== null && $entitlement->starts_at->isAfter($now)) || ($entitlement->expires_at !== null && $entitlement->expires_at->isBefore($now))) {
                continue;
            }
            $drip = $content->dripSchedules->firstWhere('entitlement_key', $rule->entitlement_key);
            $delayDays = $drip instanceof DripSchedule ? $drip->delayDays() : $rule->minimumDays();
            $availableAt = $entitlement->starts_at?->copy()->addDays($delayDays);
            if ($availableAt === null || ! $availableAt->isAfter($now)) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $data */
    private function validateContent(array $data): void
    {
        foreach (['title', 'subject_type', 'subject_key'] as $field) {
            $this->key($data[$field] ?? null, $field);
        }
        if (! in_array($data['status'] ?? 'draft', ['draft', 'published', 'archived'], true)) {
            throw ValidationException::withMessages(['status' => 'Membership content status is invalid.']);
        }
        $from = $this->date($data['available_from'] ?? null, 'available_from');
        $until = $this->date($data['available_until'] ?? null, 'available_until');
        if ($from instanceof Carbon && $until instanceof Carbon && $until->lessThanOrEqualTo($from)) {
            throw ValidationException::withMessages(['available_until' => 'Availability must end after it starts.']);
        }
    }

    private function key(mixed $value, string $field): void
    {
        if (! is_string($value) || trim($value) === '' || strlen($value) > 500) {
            throw ValidationException::withMessages([$field => 'A non-empty value within the supported length is required.']);
        }
    }

    private function date(mixed $value, string $field): ?Carbon
    {
        if ($value === null || $value instanceof Carbon) {
            return $value;
        }
        if (! is_string($value) || trim($value) === '') {
            throw ValidationException::withMessages([$field => 'The date must be a valid timestamp.']);
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            throw ValidationException::withMessages([$field => 'The date must be a valid timestamp.']);
        }
    }
}
