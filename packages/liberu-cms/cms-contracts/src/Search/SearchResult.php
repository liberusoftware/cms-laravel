<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Search;

/**
 * A single normalized search hit. Content modules build these from their own
 * models so the search module can rank and serialize results across every
 * content type without knowing any concrete class. The consumer maps `type` +
 * `slug` to its own route.
 *
 * @api This class is part of the public extension API.
 */
final class SearchResult
{
    public function __construct(
        public string $type,
        public int|string $id,
        public string $title,
        public string $slug,
        public ?string $excerpt = null,
        public float $score = 1.0,
    ) {}
}
