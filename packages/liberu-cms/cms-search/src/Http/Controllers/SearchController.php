<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Core\Support\ApiPagination;
use Liberu\Cms\Search\Http\Requests\SearchRequest;
use Liberu\Cms\Search\Http\Resources\SearchResultResource;

/**
 * Serves full-text search over published content on the Delivery API. Results
 * from every registered source are merged, ranked by score (highest first), and
 * paginated. Each source is tenant-scoped and published-only, so the aggregate
 * inherits both guarantees.
 */
final readonly class SearchController
{
    public function __construct(private SearchRegistryInterface $registry) {}

    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        $q = $request->validated('q');
        $query = is_string($q) ? trim($q) : '';

        $results = [];

        foreach ($this->registry->sources() as $source) {
            foreach ($source->search($query) as $result) {
                if ($result instanceof SearchResult) {
                    $results[] = $result;
                }
            }
        }

        usort($results, static fn (SearchResult $a, SearchResult $b): int => $b->score <=> $a->score);

        return SearchResultResource::collection(ApiPagination::fromArray($results));
    }
}
