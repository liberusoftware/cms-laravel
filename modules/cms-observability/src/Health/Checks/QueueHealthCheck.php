<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Health\Checks;

use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Throwable;

/**
 * Probes that the queue backend is reachable by asking the default connection
 * for its size. Degraded, not critical: work simply waits while it is down.
 */
final readonly class QueueHealthCheck implements HealthCheckInterface
{
    public function __construct(
        private QueueFactory $queue,
        private bool $critical,
    ) {}

    public function name(): string
    {
        return 'queue';
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function check(): bool
    {
        try {
            $this->queue->connection()->size();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
