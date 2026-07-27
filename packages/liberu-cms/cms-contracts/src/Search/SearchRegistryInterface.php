<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Search;

/**
 * The catalogue of searchable sources modules contribute. A module announces its
 * source; the search module reads the catalogue to query every content type — so
 * search tracks the installed modules without importing one. Mirrors the admin,
 * API, and sitemap registries.
 *
 * @api This interface is part of the public extension API.
 */
interface SearchRegistryInterface
{
    public function registerSource(SearchableSourceInterface $source): void;

    /**
     * @return array<int, SearchableSourceInterface>
     */
    public function sources(): array;
}
