<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplates\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentTemplates\Models\ContentTemplate;

final readonly class ContentTemplatesService
{
    /** @return LengthAwarePaginator<int, ContentTemplate> */
    public function templates(?int $teamId, ?string $contentType = null, int $perPage = 25): LengthAwarePaginator
    {
        $configuredMax = config('content-templates.pagination.max', 100);
        $maxPerPage = is_int($configuredMax) ? $configuredMax : 100;

        return ContentTemplate::query()->where('team_id', $teamId)->when($contentType !== null, fn ($q) => $q->where('content_type', $contentType))->latest('version')->paginate(max(1, min($perPage, $maxPerPage)));
    }

    /** @param array<string, mixed> $data */
    public function create(array $data, ?int $teamId = null): ContentTemplate
    {
        if (blank($data['name'] ?? null) || blank($data['slug'] ?? null) || blank($data['content_type'] ?? null) || ! is_array($data['schema'] ?? null)) {
            throw ValidationException::withMessages(['template' => 'Name, slug, content type, and schema are required.']);
        }
        if (($data['rollout_percent'] ?? 100) < 0 || ($data['rollout_percent'] ?? 100) > 100) {
            throw ValidationException::withMessages(['rollout_percent' => 'Rollout must be between 0 and 100.']);
        }

        return ContentTemplate::query()->create([...$data, 'team_id' => $teamId, 'version' => $data['version'] ?? 1]);
    }

    public function publish(ContentTemplate $template): ContentTemplate
    {
        $template->update(['published' => true]);

        return $template->fresh() ?? $template;
    }

    public function lock(ContentTemplate $template): ContentTemplate
    {
        $template->update(['locked' => true]);

        return $template->fresh() ?? $template;
    }

    public function select(string $contentType, ?int $teamId, int $bucket = 0): ?ContentTemplate
    {
        return ContentTemplate::query()->where(['team_id' => $teamId, 'content_type' => $contentType, 'published' => true])->where('rollout_percent', '>', $bucket)->latest('version')->first();
    }
}
