<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Search\Models\SearchAnalytic;

final readonly class ContentSearchService
{
    public function __construct(private SearchIndexInterface $index) {}

    /** @return array<int, SearchResult> */
    public function search(string $query, ?int $teamId = null, string $source = 'api'): array
    {
        $query = trim($query);
        $configuredMinimum = config('cms-search.min_query_length', 2);
        $minimum = is_numeric($configuredMinimum) ? (int) $configuredMinimum : 2;
        if (mb_strlen($query) < $minimum) {
            throw ValidationException::withMessages(['q' => 'The search query is too short.']);
        }
        $start = hrtime(true);
        $results = array_values(array_filter(iterator_to_array($this->index->search($query)), fn ($result): bool => $result instanceof SearchResult));
        usort($results, static fn (SearchResult $a, SearchResult $b): int => $b->score <=> $a->score);
        SearchAnalytic::query()->create(['team_id' => $teamId, 'query' => $query, 'result_count' => count($results), 'duration_ms' => (int) ((hrtime(true) - $start) / 1_000_000), 'source' => $source]);

        return $results;
    }

    /** @return LengthAwarePaginator<int, SearchAnalytic> */
    public function analytics(?int $teamId, int $perPage = 25): LengthAwarePaginator
    {
        return SearchAnalytic::query()->where('team_id', $teamId)->latest()->paginate(max(1, min($perPage, 100)));
    }

    /** @return array<int, string> */
    public function autocomplete(string $prefix, ?int $teamId = null): array
    {
        $prefix = trim($prefix);
        if ($prefix === '') {
            return [];
        }

        return array_values(SearchAnalytic::query()->where('team_id', $teamId)->where('query', 'like', $prefix.'%')->select('query')->distinct()->limit(10)->pluck('query')->map(static fn (mixed $query): string => is_string($query) ? $query : '')->all());
    }
}
