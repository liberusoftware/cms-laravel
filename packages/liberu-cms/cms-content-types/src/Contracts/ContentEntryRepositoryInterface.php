<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Contracts;

use Liberu\Cms\ContentTypes\Models\ContentEntry;

/**
 * Module-internal read boundary for content entries.
 */
interface ContentEntryRepositoryInterface
{
    public function find(int $id): ?ContentEntry;

    public function findBySlug(string $slug): ?ContentEntry;

    /**
     * Entries of a content type, by type key.
     *
     * @return array<int, ContentEntry>
     */
    public function ofType(string $typeKey): array;

    /**
     * Live entries (Published and past their publish date), newest first.
     *
     * @return array<int, ContentEntry>
     */
    public function published(): array;

    /**
     * Live entries whose title or field data matches the query.
     *
     * @return array<int, ContentEntry>
     */
    public function search(string $query, int $limit): array;
}
