<?php

declare(strict_types=1);

namespace Liberu\Cms\Redirects;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Redirects\Services\RedirectService;

final class RedirectsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new RedirectsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(RedirectService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('redirects', 'Redirects', AccessScope::Content, ['view', 'create', 'update', 'delete', 'import']));
        }
    }
}
