<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Contracts;

use Liberu\Cms\Posts\Models\Post;

/**
 * The Posts module's own read boundary; module-internal until another module
 * needs posts.
 */
interface PostRepositoryInterface
{
    public function find(int $id): ?Post;

    public function findBySlug(string $slug): ?Post;

    /**
     * Live posts (Published and past their publish date), newest first.
     *
     * @return array<int, Post>
     */
    public function published(): array;

    /**
     * Live posts flagged as featured.
     *
     * @return array<int, Post>
     */
    public function featured(): array;

    /**
     * Live posts in a category, by category slug.
     *
     * @return array<int, Post>
     */
    public function byCategory(string $categorySlug): array;

    /**
     * Live posts with a tag, by tag slug.
     *
     * @return array<int, Post>
     */
    public function byTag(string $tagSlug): array;

    /**
     * Live posts whose title or body matches the query.
     *
     * @return array<int, Post>
     */
    public function search(string $query, int $limit): array;
}
