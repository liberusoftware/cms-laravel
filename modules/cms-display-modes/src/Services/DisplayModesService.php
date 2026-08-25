<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModes\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DisplayModes\Models\DisplayMode;

final readonly class DisplayModesService
{
    public function modes(?int $teamId, ?string $contentType = null, int $perPage = 25): LengthAwarePaginator
    {
        return DisplayMode::query()->where('team_id', $teamId)->when($contentType !== null, fn ($q) => $q->where('content_type', $contentType))->latest()->paginate(max(1, min($perPage, (int) config('display-modes.pagination.max', 100))));
    }

    public function create(array $data, ?int $teamId = null): DisplayMode
    {
        if (blank($data['name'] ?? null) || blank($data['slug'] ?? null) || blank($data['content_type'] ?? null)) {
            throw ValidationException::withMessages(['mode' => 'Name, slug, and content type are required.']);
        }
        if (! in_array($data['mode_type'] ?? 'view', ['view', 'form'], true)) {
            throw ValidationException::withMessages(['mode_type' => 'The mode type is invalid.']);
        }

        return DisplayMode::query()->create([...$data, 'team_id' => $teamId]);
    }

    public function select(string $contentType, ?int $teamId, string $slug = 'default', ?string $variant = null): ?DisplayMode
    {
        $mode = DisplayMode::query()->where(['team_id' => $teamId, 'content_type' => $contentType, 'slug' => $slug, 'active' => true])->first();
        if ($variant !== null && $mode !== null && isset($mode->responsive_variants[$variant])) {
            $mode->configuration = $mode->responsive_variants[$variant];
        }

        return $mode;
    }
}
