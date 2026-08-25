<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactory;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;

final class SiteFactoryServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new SiteFactoryModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(SiteFactoryService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('site-factory', 'Site Factory', AccessScope::Module, ['view', 'create', 'update', 'clone', 'suspend', 'archive', 'teardown']));
        }
    }
}
