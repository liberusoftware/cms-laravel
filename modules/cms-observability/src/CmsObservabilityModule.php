<?php

declare(strict_types=1);

namespace Liberu\Cms\Observability;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * Observability. Serves the readiness probe (`GET /health/ready`) over a
 * module-contributed health-check registry, and records domain metrics through a
 * backend-agnostic recorder that defaults to an isolated log channel. Owns no
 * domain data and imports no feature module — removing it just stops the probe
 * and the metrics seam.
 */
final class CmsObservabilityModule extends AbstractModule
{
    public function key(): string
    {
        return 'observability';
    }

    public function name(): string
    {
        return 'Observability';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
