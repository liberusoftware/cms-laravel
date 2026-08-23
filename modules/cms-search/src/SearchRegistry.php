<?php

declare(strict_types=1);

namespace Liberu\Cms\Search;

use Liberu\Cms\Contracts\Search\SearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;

/**
 * In-memory catalogue of module-contributed searchable sources. Mirrors the
 * admin, API, and sitemap registries.
 */
final class SearchRegistry implements SearchRegistryInterface
{
    /**
     * @var array<int, SearchableSourceInterface>
     */
    private array $sources = [];

    public function registerSource(SearchableSourceInterface $source): void
    {
        $this->sources[] = $source;
    }

    public function sources(): array
    {
        return $this->sources;
    }
}
