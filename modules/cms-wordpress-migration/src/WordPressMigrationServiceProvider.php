<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigration;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\WordPressMigration\Queries\WordPressMigrationQuery;
use Liberu\Cms\WordPressMigration\Services\WordPressMigrationService;

final class WordPressMigrationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface { return new WordPressMigrationModule; }
    protected function registerModule(): void { $this->app->singleton(WordPressMigrationService::class); $this->app->singleton(WordPressMigrationQuery::class); }
    protected function bootModule(): void { $this->loadModuleMigrations(__DIR__.'/../database/migrations'); if ($this->app->bound(PermissionRegistrarInterface::class)) $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('wordpress-migration', 'WordPress Migration', AccessScope::Module, ['view', 'create', 'update', 'delete', 'process'])); }
}
