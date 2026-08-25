<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagement;

use Liberu\Cms\ConfigurationManagement\Services\ConfigurationService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ConfigurationManagementServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ConfigurationManagementModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/configuration-management.php', 'configuration-management');
        $this->app->singleton(ConfigurationService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('configuration-management', 'Configuration Management', AccessScope::Module, ['view', 'export', 'compare', 'promote', 'rollback']));
        }
    }
}
