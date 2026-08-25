<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwa;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\OfflineAndPwa\Services\OfflineAndPwaService;

final class OfflineAndPwaServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new OfflineAndPwaModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(OfflineAndPwaService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('offline-and-pwa', 'Offline and PWA', AccessScope::Module, ['view', 'create', 'update', 'delete']));
        }
    }
}
