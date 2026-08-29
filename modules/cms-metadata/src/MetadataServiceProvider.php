<?php

declare(strict_types=1);

namespace Liberu\Cms\Metadata;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Metadata\Services\MetadataService;

final class MetadataServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new MetadataModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(MetadataService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('metadata', 'Metadata', AccessScope::Content, ['view', 'create', 'update', 'delete']));
        }
    }
}
