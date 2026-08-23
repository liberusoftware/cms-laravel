<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Contracts;

use Liberu\Cms\Pages\Models\Page;

/**
 * The Pages module's own read boundary. Kept module-internal until another
 * module needs pages, at which point it is promoted to cms-contracts.
 */
interface PageRepositoryInterface
{
    public function find(int $id): ?Page;

    public function findBySlug(string $slug): ?Page;

    /**
     * Live pages: Published and past their publish date.
     *
     * @return array<int, Page>
     */
    public function published(): array;

    /**
     * Top-level pages (no parent), ordered for navigation.
     *
     * @return array<int, Page>
     */
    public function roots(): array;

    /**
     * Live pages whose title or body matches the query.
     *
     * @return array<int, Page>
     */
    public function search(string $query, int $limit): array;
}
