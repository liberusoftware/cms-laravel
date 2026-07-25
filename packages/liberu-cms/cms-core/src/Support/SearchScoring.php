<?php

declare(strict_types=1);

namespace Liberu\Cms\Core\Support;

/**
 * Shared helpers for the Delivery API search sources: the per-source row cap and
 * a simple relevance score that ranks a title match above a body-only match.
 * Kept in one place so every content source ranks consistently.
 *
 * @internal Used by content modules' search sources.
 */
final class SearchScoring
{
    public static function perSourceLimit(): int
    {
        $limit = config('cms-search.per_source_limit', 50);

        return is_numeric($limit) ? max(1, (int) $limit) : 50;
    }

    /**
     * 2.0 when the query appears in the title, otherwise 1.0 (a body-only match).
     */
    public static function score(string $title, string $query): float
    {
        return stripos($title, $query) !== false ? 2.0 : 1.0;
    }
}
