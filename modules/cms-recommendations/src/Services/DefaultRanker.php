<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations\Services;

use Liberu\Cms\Recommendations\Contracts\Ranker;
use Liberu\Cms\Recommendations\Models\RecommendationItem;

final class DefaultRanker implements Ranker
{
    /** @return array<int, RecommendationItem> */
    public function score(iterable $items, array $context = []): array
    {
        $items = is_array($items) ? $items : iterator_to_array($items);
        usort($items, static fn (RecommendationItem $left, RecommendationItem $right): int => $right->editorial_score <=> $left->editorial_score ?: $right->popularity_score <=> $left->popularity_score ?: $right->position <=> $left->position);

        return $items;
    }
}
