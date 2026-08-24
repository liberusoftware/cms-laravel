<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperations;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\SecurityOperations\Services\SecurityOperationsService;

final class SecurityOperationsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new SecurityOperationsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(SecurityOperationsService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('security-operations', 'Security Operations', AccessScope::Module, ['view', 'scan', 'integrity', 'manage']));
        }
    }
}
