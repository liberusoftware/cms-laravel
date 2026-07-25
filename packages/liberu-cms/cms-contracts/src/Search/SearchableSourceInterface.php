<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Search;

/**
 * A content type that can be searched. Each content module implements this for
 * its own model and registers it with the search registry, so the search module
 * queries every content type without ever importing a module. Implementations
 * must return only published content; tenant scoping is applied by the shared
 * tenancy scope during the request.
 */
interface SearchableSourceInterface
{
    /**
     * Published items matching the query, as normalized results.
     *
     * @return iterable<int, SearchResult>
     */
    public function search(string $query): iterable;
}
