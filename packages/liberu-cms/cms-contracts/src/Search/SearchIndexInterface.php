<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Search;

/**
 * The swappable backend that actually executes a search, sitting *below* the
 * registered {@see SearchableSourceInterface} sources. It changes *how* search
 * runs — a portable database `LIKE` in development, Meilisearch via Scout in
 * production — never the query surface or the {@see SearchResult} shape. Selected
 * by `cms-search.driver`; the controller calls the configured driver and ranks
 * and paginates the results it returns.
 *
 * @api This interface is part of the public extension API.
 */
interface SearchIndexInterface
{
    /**
     * Matches for the query across every registered source, as normalized
     * results (unranked; the caller applies the ranking and pagination).
     *
     * @return iterable<int, SearchResult>
     */
    public function search(string $query): iterable;

    /**
     * Whether the backing index is reachable right now, for the readiness probe.
     */
    public function isReady(): bool;
}
