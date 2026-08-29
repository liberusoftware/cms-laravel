<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectory;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\IntegrationDirectory\Services\IntegrationDirectoryService;

final class IntegrationDirectoryServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new IntegrationDirectoryModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(IntegrationDirectoryService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('integration-directory', 'Integration Directory', AccessScope::Module, ['view', 'create', 'update', 'delete']));
        }
    }
}
