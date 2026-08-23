<?php

declare(strict_types=1);

namespace Liberu\Cms\Admin;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class AdminServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new AdminModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/admin.php', 'cms-admin');

        $this->app->singleton(AdminResourceRegistryInterface::class, AdminResourceRegistry::class);
        $this->app->singleton(AdminDashboardRegistryInterface::class, AdminDashboardRegistry::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleViews(__DIR__.'/../resources/views', 'cms-admin');

        $this->declarePermissions($this->app->make(PermissionRegistrarInterface::class));

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/admin.php' => $this->app->configPath('cms-admin.php'),
            ], 'cms-admin-config');
        }
    }

    private function declarePermissions(PermissionRegistrarInterface $registrar): void
    {
        $registrar->register(new PermissionGroup('modules', 'Modules', AccessScope::Module, [
            'view', 'manage',
        ]));
    }
}
