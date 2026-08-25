<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModes;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\DisplayModes\Services\DisplayModesService;

final class DisplayModesServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new DisplayModesModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/display-modes.php', 'display-modes');
        $this->app->singleton(DisplayModesService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('display-modes', 'Display Modes', AccessScope::Content, ['view', 'create', 'update', 'delete']));
        }
    }
}
