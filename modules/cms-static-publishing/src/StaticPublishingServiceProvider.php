<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishing;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\StaticPublishing\Queries\StaticPublishingQuery;
use Liberu\Cms\StaticPublishing\Services\StaticPublishingService;
use Liberu\Cms\StaticPublishing\Support\DeploymentAdapterRegistry;

final class StaticPublishingServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new StaticPublishingModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(StaticPublishingService::class);
        $this->app->singleton(StaticPublishingQuery::class);
        $this->app->singleton(DeploymentAdapterRegistry::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('static-publishing', 'Static Publishing', AccessScope::Module, ['view', 'build', 'preview', 'rollback', 'invalidate', 'deploy']));
        }
    }
}
