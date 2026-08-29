<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformance;

use Liberu\Cms\Core\Module\AbstractModule;

final class CacheAndPerformanceModule extends AbstractModule
{
    public function key(): string
    {
        return 'cache-and-performance';
    }

    public function name(): string
    {
        return 'Cache and Performance';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
