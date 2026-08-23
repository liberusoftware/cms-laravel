<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Http\Controllers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Liberu\Cms\Contracts\Metrics\MetricsRecorderInterface;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Core\Support\ApiPagination;
use Liberu\Cms\Search\Http\Requests\SearchRequest;
use Liberu\Cms\Search\Http\Resources\SearchResultResource;

/**
 * Serves full-text search over published content on the Delivery API. Matching
 * is delegated to the configured search driver; the results it returns are
 * ranked by score (highest first) and paginated. Each source is tenant-scoped
 * and published-only, so the aggregate inherits both guarantees. Query count and
 * latency are recorded through the metrics seam when it is bound.
 */
final readonly class SearchController
{
    public function __construct(
        private SearchIndexInterface $index,
        private Container $container,
    ) {}

    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        $q = $request->validated('q');
        $query = is_string($q) ? trim($q) : '';

        $start = microtime(true);

        $results = [];

        foreach ($this->index->search($query) as $result) {
            if ($result instanceof SearchResult) {
                $results[] = $result;
            }
        }

        usort($results, static fn (SearchResult $a, SearchResult $b): int => $b->score <=> $a->score);

        $this->recordMetrics(count($results), (microtime(true) - $start) * 1000);

        return SearchResultResource::collection(ApiPagination::fromArray($results));
    }

    private function recordMetrics(int $resultCount, float $milliseconds): void
    {
        if (! $this->container->bound(MetricsRecorderInterface::class)) {
            return;
        }

        /** @var MetricsRecorderInterface $recorder */
        $recorder = $this->container->make(MetricsRecorderInterface::class);

        $recorder->increment('search.query');
        $recorder->timing('search.latency', $milliseconds);
        $recorder->gauge('search.results', $resultCount);
    }
}
