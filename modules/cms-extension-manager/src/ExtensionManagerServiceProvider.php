<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManager;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\ExtensionManager\Services\ExtensionManagerService;

final class ExtensionManagerServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ExtensionManagerModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(ExtensionManagerService::class);
    }

    protected function bootModule(): void
    {
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('extension-manager', 'Extension Manager', AccessScope::Module, ['view', 'enable', 'disable']));
        }
    }
}
