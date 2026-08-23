<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Index;

use Illuminate\Database\ConnectionResolverInterface;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Throwable;

/**
 * The default, zero-infra search driver: a portable `LIKE` over the published
 * content repositories, delegated to each registered source's `search()`. Keeps
 * the sources' `SearchScoring` ranking, so behaviour is unchanged from before the
 * driver seam existed. Reachable whenever the database is.
 */
final readonly class DatabaseSearchIndex implements SearchIndexInterface
{
    public function __construct(
        private SearchRegistryInterface $registry,
        private ConnectionResolverInterface $connections,
    ) {}

    public function search(string $query): iterable
    {
        foreach ($this->registry->sources() as $source) {
            yield from $source->search($query);
        }
    }

    public function isReady(): bool
    {
        try {
            $this->connections->connection()->select('select 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
