<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Health\Checks;

use Illuminate\Database\ConnectionResolverInterface;
use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Throwable;

/**
 * Probes that the default database connection is reachable by issuing a trivial
 * query. The only check critical by default: the app cannot serve without it.
 */
final readonly class DatabaseHealthCheck implements HealthCheckInterface
{
    public function __construct(
        private ConnectionResolverInterface $connections,
        private bool $critical,
    ) {}

    public function name(): string
    {
        return 'database';
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function check(): bool
    {
        try {
            $this->connections->connection()->select('select 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
