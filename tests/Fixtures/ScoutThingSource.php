<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Liberu\Cms\Contracts\Search\ScoutSearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchResult;

/**
 * A searchable source that opts into the Scout driver, backed by
 * {@see SearchableThing}. Its database `search()` is intentionally empty — the
 * fixture exists only to prove the Scout path selects `scoutSearch()`.
 */
final class ScoutThingSource implements ScoutSearchableSourceInterface
{
    public function search(string $query): iterable
    {
        return [];
    }

    public function scoutSearch(string $query): iterable
    {
        foreach (SearchableThing::search($query)->get() as $thing) {
            yield new SearchResult(
                type: 'thing',
                id: $thing->id,
                title: $thing->title,
                slug: (string) $thing->id,
            );
        }
    }
}
