<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModes\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\DisplayModes\Models\DisplayMode;

final readonly class DisplayModesService
{
    /** @return LengthAwarePaginator<int, DisplayMode> */
    public function modes(?int $teamId, ?string $contentType = null, int $perPage = 25): LengthAwarePaginator
    {
        $configuredMax = config('display-modes.pagination.max', 100);
        $maxPerPage = is_int($configuredMax) ? $configuredMax : 100;

        return DisplayMode::query()->where('team_id', $teamId)->when($contentType !== null, fn ($q) => $q->where('content_type', $contentType))->latest()->paginate(max(1, min($perPage, $maxPerPage)));
    }

    /** @param array<string, mixed> $data */
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
        $responsiveVariants = $mode?->responsive_variants;
        if ($variant !== null && $mode !== null && is_array($responsiveVariants) && isset($responsiveVariants[$variant]) && is_array($responsiveVariants[$variant])) {
            $mode->configuration = $this->normalized($responsiveVariants[$variant]);
        }

        return $mode;
    }

    /**
     * @param  array<mixed, mixed>  $value
     * @return array<string, mixed>
     */
    private function normalized(array $value): array
    {
        $data = [];
        foreach ($value as $key => $item) {
            if (is_string($key)) {
                $data[$key] = $item;
            }
        }

        return $data;
    }
}
