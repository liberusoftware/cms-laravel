<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Health;

/**
 * The catalogue of readiness health checks modules contribute. A module owning a
 * dependency registers its probe here; the observability module reads the
 * catalogue when it serves `GET /health/ready` — so readiness tracks the
 * installed modules without the observability module importing one. Mirrors the
 * sitemap, admin, and API registries.
 *
 * @api This interface is part of the public extension API.
 */
interface HealthCheckRegistryInterface
{
    public function register(HealthCheckInterface $check): void;

    /**
     * @return iterable<int, HealthCheckInterface>
     */
    public function all(): iterable;
}
