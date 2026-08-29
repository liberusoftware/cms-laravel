<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegration;

use Liberu\Cms\AnalyticsIntegration\Services\AnalyticsIntegrationService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class AnalyticsIntegrationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new AnalyticsIntegrationModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(AnalyticsIntegrationService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('analytics-integration', 'Analytics Integration', AccessScope::Module, ['view', 'record', 'manage-mappings', 'manage-adapters']));
        }
    }
}
