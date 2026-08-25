<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederation;

use Liberu\Cms\ContentFederation\Services\ContentFederationService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentFederationServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentFederationModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/content-federation.php', 'content-federation');
        $this->app->singleton(ContentFederationService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('content-federation', 'Content Federation', AccessScope::Module, ['view', 'create', 'update', 'delete', 'revalidate']));
        }
    }
}
