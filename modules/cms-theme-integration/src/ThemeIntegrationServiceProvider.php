<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegration;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\ThemeIntegration\Queries\ThemeIntegrationQuery;
use Liberu\Cms\ThemeIntegration\Services\ThemeIntegrationService;

final class ThemeIntegrationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ThemeIntegrationModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/theme-integration.php', 'theme-integration');
        $this->app->singleton(ThemeIntegrationService::class);
        $this->app->singleton(ThemeIntegrationQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('theme-integration', 'Theme Integration', AccessScope::Module, ['view', 'create', 'update', 'delete']));
        }
    }
}
