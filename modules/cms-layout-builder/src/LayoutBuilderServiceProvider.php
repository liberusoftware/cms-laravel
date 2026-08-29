<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilder;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\LayoutBuilder\Services\LayoutBuilderService;

final class LayoutBuilderServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new LayoutBuilderModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/layout-builder.php', 'layout-builder');
        $this->app->singleton(LayoutBuilderService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('layout-builder', 'Layout Builder', AccessScope::Content, ['view', 'create', 'update', 'delete', 'publish']));
        }
    }
}
