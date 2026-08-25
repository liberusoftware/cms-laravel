<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplace;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\ThemeMarketplace\Queries\ThemeMarketplaceQuery;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;

final class ThemeMarketplaceServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ThemeMarketplaceModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(ThemeMarketplaceService::class);
        $this->app->singleton(ThemeMarketplaceQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('theme-marketplace', 'Theme Marketplace', AccessScope::Module, ['view', 'create', 'update', 'delete', 'review']));
        }
    }
}
