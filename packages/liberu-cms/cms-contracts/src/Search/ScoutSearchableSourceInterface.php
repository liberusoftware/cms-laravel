<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Search;

/**
 * A searchable source that can also be queried through Laravel Scout. A content
 * module implements this in addition to {@see SearchableSourceInterface} to opt
 * its model into the Scout driver: when `cms-search.driver` is `scout`, the
 * driver calls {@see scoutSearch()} (a `Model::search()` query) instead of the
 * database `search()`. Sources that do not implement this keep using their
 * database search under the Scout driver, so opting in is per-module and gradual.
 *
 * @api This interface is part of the public extension API.
 */
interface ScoutSearchableSourceInterface extends SearchableSourceInterface
{
    /**
     * Published items matching the query via Scout, as normalized results.
     *
     * @return iterable<int, SearchResult>
     */
    public function scoutSearch(string $query): iterable;
}
