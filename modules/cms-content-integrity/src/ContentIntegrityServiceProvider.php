<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrity;

use Liberu\Cms\ContentIntegrity\Services\ContentIntegrityService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentIntegrityServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentIntegrityModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-integrity.php', 'content-integrity');
        $this->app->singleton(ContentIntegrityService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-integrity', 'Content Integrity', AccessScope::Content, ['view', 'scan', 'repair', 'resolve']));
        }
    }
}
