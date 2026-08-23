<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Search;

use Liberu\Cms\Contracts\Search\SearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Core\Support\SearchScoring;
use Liberu\Cms\Pages\Contracts\PageRepositoryInterface;

/**
 * Searches published Pages for the Delivery API search endpoint.
 */
final readonly class PageSearchSource implements SearchableSourceInterface
{
    public function __construct(private PageRepositoryInterface $pages) {}

    public function search(string $query): iterable
    {
        foreach ($this->pages->search($query, SearchScoring::perSourceLimit()) as $page) {
            yield new SearchResult(
                type: 'page',
                id: $page->id,
                title: $page->title,
                slug: $page->slug,
                excerpt: $page->excerpt,
                score: SearchScoring::score($page->title, $query),
            );
        }
    }
}
