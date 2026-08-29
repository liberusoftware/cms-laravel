<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformance;

use Liberu\Cms\CacheAndPerformance\Services\CacheAndPerformanceService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class CacheAndPerformanceServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CacheAndPerformanceModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(CacheAndPerformanceService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('cache-and-performance', 'Cache and Performance', AccessScope::Module, ['view', 'warm', 'invalidate', 'diagnostics']));
        }
    }
}
