<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Search;

use Illuminate\Database\Eloquent\Collection;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Models\ContentType;
use Liberu\Cms\Contracts\Search\SearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Core\Support\SearchScoring;

/**
 * Searches published Content-Entries for the Delivery API search endpoint. The
 * result `type` is the entry's content-type key so the consumer can build the
 * `/content/{type}/{slug}` link.
 */
final readonly class ContentEntrySearchSource implements SearchableSourceInterface
{
    public function __construct(private ContentEntryRepositoryInterface $entries) {}

    public function search(string $query): iterable
    {
        $entries = new Collection($this->entries->search($query, SearchScoring::perSourceLimit()));
        $entries->loadMissing('type');

        foreach ($entries as $entry) {
            $type = $entry->type;

            yield new SearchResult(
                type: $type instanceof ContentType ? $type->key : 'content-entry',
                id: $entry->id,
                title: $entry->title,
                slug: $entry->slug,
                score: SearchScoring::score($entry->title, $query),
            );
        }
    }
}
