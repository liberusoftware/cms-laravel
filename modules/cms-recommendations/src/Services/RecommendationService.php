<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations\Services;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Recommendations\Contracts\Ranker;
use Liberu\Cms\Recommendations\Models\RecommendationItem;
use Liberu\Cms\Recommendations\Models\RecommendationList;

final class RecommendationService
{
    public function createList(string $key, string $name, string $kind = 'latest', array $audienceRules = [], array $exclusions = [], ?int $teamId = null): RecommendationList
    {
        if (trim($key) === '' || trim($name) === '') {
            throw ValidationException::withMessages(['name' => 'Recommendation list key and name are required.']);
        }
        if (! in_array($kind, ['latest', 'popular', 'trending', 'editorial'], true)) {
            throw ValidationException::withMessages(['kind' => 'Unsupported recommendation list kind.']);
        }

        return RecommendationList::query()->create(['key' => $key, 'name' => $name, 'kind' => $kind, 'audience_rules' => $audienceRules, 'exclusions' => array_values($exclusions), 'team_id' => $teamId]);
    }

    public function addItem(RecommendationList $list, array $attributes): RecommendationItem
    {
        foreach (['item_type', 'item_key', 'title'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw ValidationException::withMessages([$field => 'Recommendation item field is required.']);
            }
        }

        return $list->items()->updateOrCreate(['item_type' => $attributes['item_type'], 'item_key' => $attributes['item_key']], array_intersect_key($attributes, array_flip(['title', 'summary', 'context', 'popularity_score', 'editorial_score', 'published_at', 'position'])) + ['item_type' => $attributes['item_type']]);
    }

    public function exclude(RecommendationList $list, string $itemKey): RecommendationList
    {
        if (trim($itemKey) === '') {
            throw ValidationException::withMessages(['item_key' => 'An item key is required.']);
        }
        $list->update(['exclusions' => array_values(array_unique([...($list->exclusions ?? []), $itemKey]))]);

        return $list->refresh();
    }

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
        return array_all($rules, fn ($expected, $key): bool => is_array($expected)
            ? in_array(Arr::get($context, $key), $expected, true)
            : Arr::get($context, $key) === $expected);
    }
}
