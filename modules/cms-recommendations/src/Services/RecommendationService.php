<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations\Services;

use Illuminate\Support\Arr;
use Liberu\Cms\Recommendations\Contracts\Ranker;
use Liberu\Cms\Recommendations\Models\RecommendationItem;
use Liberu\Cms\Recommendations\Models\RecommendationList;

final class RecommendationService
{
    /** @return array<int, array<string, mixed>> */
    public function recommend(string $listKey, array $context = [], ?string $excludeKey = null, int $limit = 10): array
    {
        $list = RecommendationList::query()->where('key', $listKey)->where('active', true)->first();
        if ($list === null || ! $this->eligible($list->audience_rules, $context)) {
            return [];
        }

        $excluded = array_filter(array_merge($list->exclusions, $excludeKey === null ? [] : [$excludeKey]));
        $items = $list->items()->get()->filter(fn (RecommendationItem $item): bool => ! in_array($item->item_key, $excluded, true));
        $items = match ($list->kind) {
            'latest' => $items->sortByDesc('published_at'),
            'popular', 'trending' => $items->sortByDesc('popularity_score'),
            'editorial' => $items->sortByDesc('editorial_score'),
            default => $items,
        };

        $ranker = app(Ranker::class);
        $items = $ranker->score($items->values()->all(), $context);

        return array_map(static fn (RecommendationItem $item): array => [
            'type' => $item->item_type, 'key' => $item->item_key, 'title' => $item->title, 'summary' => $item->summary,
            'explanation' => $item->context['explanation'] ?? ucfirst($item->list->kind).' recommendation', 'score' => $item->editorial_score ?: $item->popularity_score,
        ], array_slice($items, 0, max(1, min(100, $limit))));
    }

    private function eligible(array $rules, array $context): bool
    {
        foreach ($rules as $key => $expected) {
            if (is_array($expected) ? ! in_array(Arr::get($context, $key), $expected, true) : Arr::get($context, $key) !== $expected) {
                return false;
            }
        }

        return true;
    }
}
