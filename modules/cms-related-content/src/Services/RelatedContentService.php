<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContent\Services;

use Illuminate\Validation\ValidationException;
use Liberu\Cms\RelatedContent\Models\RelatedContent;

final class RelatedContentService
{
    public function relate(string $sourceType, int $sourceId, string $targetType, int $targetId, string $mode = 'manual', float $score = 1, array $taxonomy = [], ?int $teamId = null): RelatedContent
    {
        if ($sourceType === $targetType && $sourceId === $targetId) {
            throw ValidationException::withMessages(['target_id' => 'Content cannot relate to itself.']);
        }
        if (! in_array($mode, ['manual', 'rule', 'search', 'similarity', 'recency', 'taxonomy'], true)) {
            throw ValidationException::withMessages(['mode' => 'Unsupported relationship mode.']);
        }

        return RelatedContent::query()->updateOrCreate(['source_type' => $sourceType, 'source_id' => $sourceId, 'target_type' => $targetType, 'target_id' => $targetId, 'team_id' => $teamId], ['mode' => $mode, 'score' => max(0, min(1, $score)), 'taxonomy' => $taxonomy, 'explanation' => ['mode' => $mode, 'score' => $score], 'excluded' => false]);
    }

    public function exclude(string $sourceType, int $sourceId, string $targetType, int $targetId, ?int $teamId = null): RelatedContent
    {
        return RelatedContent::query()->updateOrCreate(['source_type' => $sourceType, 'source_id' => $sourceId, 'target_type' => $targetType, 'target_id' => $targetId, 'team_id' => $teamId], ['mode' => 'manual', 'excluded' => true, 'explanation' => ['reason' => 'explicit-exclusion']]);
    }

    /** @return array<int, array<string, mixed>> */
    public function related(string $sourceType, int $sourceId, int $limit = 10, array $taxonomy = [], ?int $teamId = null): array
    {
        $query = RelatedContent::query()->where('source_type', $sourceType)->where('source_id', $sourceId)->where('excluded', false)->where('team_id', $teamId)->orderByDesc('score')->orderByDesc('updated_at');
        if ($taxonomy !== []) {
            $query->where(function ($builder) use ($taxonomy): void {
                foreach ($taxonomy as $key => $value) {
                    $builder->orWhereJsonContains('taxonomy->'.$key, $value);
                }
            });
        }

        return $query->limit(max(1, min(100, $limit)))->get()->map(fn (RelatedContent $item): array => ['target_type' => $item->target_type, 'target_id' => $item->target_id, 'score' => $item->score, 'mode' => $item->mode, 'explanation' => $item->explanation])->all();
    }
}
