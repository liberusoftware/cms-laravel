<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigration;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\JoomlaMigration\Services\JoomlaMigrationService;

final class JoomlaMigrationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new JoomlaMigrationModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(JoomlaMigrationService::class);
    }

    protected function bootModule(): void
    {
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('joomla-migration', 'Joomla Migration', AccessScope::Module, ['view', 'create', 'process']));
        }
    }
}
