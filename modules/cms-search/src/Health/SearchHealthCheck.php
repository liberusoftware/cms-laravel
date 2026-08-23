<?php

declare(strict_types=1);

namespace Liberu\Cms\Search\Health;

use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Liberu\Cms\Contracts\Search\SearchIndexInterface;
use Throwable;

/**
 * Readiness probe for search: asks the *active* driver whether its index is
 * reachable, so it reports on the database or Scout backend the app is actually
 * configured to use. Degraded, not critical — the app still serves content while
 * search is unavailable.
 *
 * Contributed to the observability readiness registry through the contract; the
 * Search module imports nothing from cms-observability.
 */
final readonly class SearchHealthCheck implements HealthCheckInterface
{
    public function __construct(
        private SearchIndexInterface $index,
        private bool $critical,
    ) {}

    public function name(): string
    {
        return 'search';
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function check(): bool
    {
        try {
            return $this->index->isReady();
        } catch (Throwable) {
            return false;
        }
    }
}
