<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigration;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\DrupalMigration\Services\DrupalMigrationService;

final class DrupalMigrationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new DrupalMigrationModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(DrupalMigrationService::class);
    }

    protected function bootModule(): void
    {
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('drupal-migration', 'Drupal Migration', AccessScope::Module, ['view', 'create', 'process']));
        }
    }
}
