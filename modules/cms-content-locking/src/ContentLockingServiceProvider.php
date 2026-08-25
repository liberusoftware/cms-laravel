<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLocking;

use Liberu\Cms\ContentLocking\Services\ContentLockingService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentLockingServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentLockingModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-locking.php', 'content-locking');
        $this->app->singleton(ContentLockingService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-locking', 'Content Locking', AccessScope::Content, ['view', 'acquire', 'renew', 'release', 'merge']));
        }
    }
}
