<?php

declare(strict_types=1);

namespace Liberu\Cms\Publishing;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Publishing\Services\PublishingService;

final class PublishingServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new PublishingModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(PublishingService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('publishing', 'Publishing', AccessScope::Content, ['view', 'publish', 'unpublish', 'archive']));
        }
    }
}
