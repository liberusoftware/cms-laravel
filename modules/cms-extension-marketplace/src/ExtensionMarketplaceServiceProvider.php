<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplace;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\ExtensionMarketplace\Queries\ExtensionMarketplaceQuery;
use Liberu\Cms\ExtensionMarketplace\Services\ExtensionMarketplaceService;

final class ExtensionMarketplaceServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ExtensionMarketplaceModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(ExtensionMarketplaceService::class);
        $this->app->singleton(ExtensionMarketplaceQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('extension-marketplace', 'Extension Marketplace', AccessScope::Module, ['view', 'create', 'update', 'delete', 'publish', 'review']));
        }
    }
}
