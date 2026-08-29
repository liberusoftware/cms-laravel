<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestore;

use Liberu\Cms\BackupAndRestore\Services\BackupAndRestoreService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class BackupAndRestoreServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new BackupAndRestoreModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/backup-and-restore.php', 'backup-and-restore');
        $this->app->singleton(BackupAndRestoreService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('backup-and-restore', 'Backup and Restore', AccessScope::Module, ['view', 'create', 'verify', 'restore', 'delete']));
        }
    }
}
