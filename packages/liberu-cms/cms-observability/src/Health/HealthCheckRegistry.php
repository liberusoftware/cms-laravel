<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability\Health;

use Liberu\Cms\Contracts\Health\HealthCheckInterface;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;

/**
 * In-memory catalogue of module-contributed readiness checks. Mirrors the
 * sitemap, admin, and API registries.
 */
final class HealthCheckRegistry implements HealthCheckRegistryInterface
{
    /**
     * @var array<int, HealthCheckInterface>
     */
    private array $checks = [];

    public function register(HealthCheckInterface $check): void
    {
        $this->checks[] = $check;
    }

    public function all(): iterable
    {
        return $this->checks;
    }
}
