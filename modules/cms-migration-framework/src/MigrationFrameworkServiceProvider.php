<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFramework;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\MigrationFramework\Services\MigrationFrameworkService;

final class MigrationFrameworkServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new MigrationFrameworkModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(MigrationFrameworkService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('migration-framework', 'Migration Framework', AccessScope::Module, ['view', 'create', 'update', 'process']));
        }
    }
}
