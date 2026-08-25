<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccess;

use Liberu\Cms\ContentAccess\Services\ContentAccessService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentAccessServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentAccessModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-access.php', 'content-access');
        $this->app->singleton(ContentAccessService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-access', 'Content Access', AccessScope::Content, ['view', 'create', 'update', 'delete', 'preview']));
        }
    }
}
