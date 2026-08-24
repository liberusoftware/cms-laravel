<?php

declare(strict_types=1);

namespace Liberu\Cms\Recommendations\Contracts;

use Liberu\Cms\Recommendations\Models\RecommendationItem;

interface Ranker
{
    /** @param iterable<RecommendationItem> $items */
    public function score(iterable $items, array $context = []): array;
}
