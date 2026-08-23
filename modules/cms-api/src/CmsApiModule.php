<?php

declare(strict_types=1);

namespace Liberu\Cms\Api;

use Liberu\Cms\Core\Module\AbstractModule;

/**
 * CMS API. Provides the headless Delivery API: the versioned route group,
 * Sanctum Team-token authentication, the request tenant context, per-token rate
 * limiting, and CORS. It consumes only contracts and the core module system, so
 * it can be enabled or removed without dragging any content module along.
 */
final class CmsApiModule extends AbstractModule
{
    public function key(): string
    {
        return 'api';
    }

    public function name(): string
    {
        return 'CMS API';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
