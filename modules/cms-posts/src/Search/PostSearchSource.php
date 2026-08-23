<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Search;

use Liberu\Cms\Contracts\Search\SearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchResult;
use Liberu\Cms\Core\Support\SearchScoring;
use Liberu\Cms\Posts\Contracts\PostRepositoryInterface;

/**
 * Searches published Posts for the Delivery API search endpoint.
 */
final readonly class PostSearchSource implements SearchableSourceInterface
{
    public function __construct(private PostRepositoryInterface $posts) {}

    public function search(string $query): iterable
    {
        foreach ($this->posts->search($query, SearchScoring::perSourceLimit()) as $post) {
            yield new SearchResult(
                type: 'post',
                id: $post->id,
                title: $post->title,
                slug: $post->slug,
                excerpt: $post->excerpt,
                score: SearchScoring::score($post->title, $query),
            );
        }
    }
}
