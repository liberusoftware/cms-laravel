<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections;

use Liberu\Cms\Collections\Filament\CollectionItemResource;
use Liberu\Cms\Collections\Filament\CollectionResource;
use Liberu\Cms\Collections\Http\Controllers\CollectionApiController;
use Liberu\Cms\Collections\Actions\CollectionMutationService;
use Liberu\Cms\Collections\Queries\CollectionQuery;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class CollectionsServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new CollectionsModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(CollectionQuery::class);
        $this->app->singleton(CollectionMutationService::class);
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('collections', CollectionResource::class);
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('collection-items', CollectionItemResource::class);
        }
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('collections', new ApiEndpoint('collections', CollectionApiController::class, 'index', 'collections.index'));
            $registry->registerEndpoint('collections', new ApiEndpoint('collections/{slug}', CollectionApiController::class, 'show', 'collections.show'));
            $registry->registerEndpoint('collections/{collection}/items', new ApiEndpoint('collections/{collection}/items', CollectionApiController::class, 'items', 'collections.items'));
            $registry->registerEndpoint('collections/{collection}/items/{slug}', new ApiEndpoint('collections/{collection}/items/{slug}', CollectionApiController::class, 'item', 'collections.item'));
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('collections', 'Collections', AccessScope::Content, ['view', 'create', 'update', 'delete']),
            );
        }
    }
}
