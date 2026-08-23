<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Health\Checks;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Throwable;

/**
 * Probes that the cache store is reachable by reading a reserved key. Degraded,
 * not critical: the app can still serve with the cache down.
 */
final readonly class CacheHealthCheck implements HealthCheckInterface
{
    private const string PROBE_KEY = '__cms_readiness_probe__';

    public function __construct(
        private CacheFactory $cache,
        private bool $critical,
    ) {}

    public function name(): string
    {
        return 'cache';
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function check(): bool
    {
        try {
            $this->cache->store()->get(self::PROBE_KEY);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
