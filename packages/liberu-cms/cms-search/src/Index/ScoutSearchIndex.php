<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Index;

use Laravel\Scout\EngineManager;
use Liberu\Cms\Contracts\Search\ScoutSearchableSourceInterface;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Throwable;

/**
 * The opt-in production driver: Meilisearch through Laravel Scout. A source is
 * queried through Scout only when it opts in via
 * {@see ScoutSearchableSourceInterface}; any other source keeps its database
 * `search()`, so enabling the driver is safe before every module has adopted
 * Scout.
 */
final readonly class ScoutSearchIndex implements SearchIndexInterface
{
    public function __construct(
        private SearchRegistryInterface $registry,
        private EngineManager $engines,
    ) {}

    public function search(string $query): iterable
    {
        foreach ($this->registry->sources() as $source) {
            yield from $source instanceof ScoutSearchableSourceInterface
                ? $source->scoutSearch($query)
                : $source->search($query);
        }
    }

    /**
     * Reports readiness by confirming the configured Scout engine resolves. For
     * the in-process engines (collection/database/null) that is genuine
     * reachability. For a remote backend it proves the engine is *configured*,
     * not that Meilisearch answers right now: a deep index ping is deferred as a
     * documented seam (it cannot be verified on this stack, matching the phase's
     * honesty bar for Redis / Octane / Meilisearch). An operator wanting a true
     * remote health signal binds a recorder / probe against their Meilisearch.
     */
    public function isReady(): bool
    {
        try {
            $this->engines->engine();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
