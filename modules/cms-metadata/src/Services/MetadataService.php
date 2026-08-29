<?php

declare(strict_types=1);

namespace Liberu\Cms\Metadata\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\Metadata\Models\MetadataEntry;

final class MetadataService
{
    /** @return array<string, mixed> */
    public function all(string $subjectType, int|string $subjectId): array
    {
        $this->validateSubject($subjectType, $subjectId);

        return MetadataEntry::query()->where('subject_type', $subjectType)->where('subject_id', (string) $subjectId)->pluck('value', 'key')->all();
    }

    public function get(string $subjectType, int|string $subjectId, string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        return MetadataEntry::query()->where('subject_type', $subjectType)->where('subject_id', (string) $subjectId)->where('key', $key)->value('value') ?? $default;
    }

    public function set(string $subjectType, int|string $subjectId, string $key, mixed $value, ?int $teamId = null): MetadataEntry
    {
        $this->validateSubject($subjectType, $subjectId);
        $this->validateKey($key);
        if (is_resource($value)) {
            throw ValidationException::withMessages(['value' => 'Metadata values must be JSON serializable.']);
        }
        try {
            json_encode($value, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::withMessages(['value' => 'Metadata values must be JSON serializable.']);
        }

        return MetadataEntry::query()->updateOrCreate(
            ['subject_type' => $subjectType, 'subject_id' => (string) $subjectId, 'key' => $key],
            ['value' => $value, 'value_type' => get_debug_type($value), 'team_id' => $teamId],
        );
    }

    public function remove(string $subjectType, int|string $subjectId, string $key): bool
    {
        $this->validateSubject($subjectType, $subjectId);
        $this->validateKey($key);

        return MetadataEntry::query()->where('subject_type', $subjectType)->where('subject_id', (string) $subjectId)->where('key', $key)->delete() > 0;
    }

    private function validateSubject(string $subjectType, int|string $subjectId): void
    {
        if (preg_match('/\\A[a-zA-Z0-9_\\\\.-]+\\z/', $subjectType) !== 1 || trim((string) $subjectId) === '') {
            throw ValidationException::withMessages(['subject' => 'A valid subject type and identifier are required.']);
        }
    }

    private function validateKey(string $key): void
    {
        if (preg_match('/\\A[a-zA-Z][a-zA-Z0-9_.:-]{0,119}\\z/', $key) !== 1) {
            throw ValidationException::withMessages(['key' => 'Metadata keys must be 1–120 characters and use letters, numbers, dots, colons, underscores, or hyphens.']);
        }
    }
}
