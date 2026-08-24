<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgets;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\RegionsAndWidgets\Services\RegionWidgetService;

final class RegionsAndWidgetsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new RegionsAndWidgetsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(RegionWidgetService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('regions-and-widgets', 'Regions and Widgets', AccessScope::Content, ['view', 'create', 'update', 'delete', 'place', 'publish']));
        }
    }
}
