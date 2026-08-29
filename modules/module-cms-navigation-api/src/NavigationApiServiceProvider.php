<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\NavigationApi\Http\NavigationApiController;

final class NavigationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $write = ['abilities:content:write'];
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation', NavigationApiController::class, 'index', 'cms.navigation.list'));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation/{id}', NavigationApiController::class, 'show', 'cms.navigation.get'));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation', NavigationApiController::class, 'store', 'cms.navigation.create', 'POST', $write));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation/{id}', NavigationApiController::class, 'update', 'cms.navigation.update', 'PATCH', $write));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation/{id}', NavigationApiController::class, 'destroy', 'cms.navigation.delete', 'DELETE', $write));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation/{id}/items', NavigationApiController::class, 'storeItem', 'cms.navigation.item.create', 'POST', $write));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation/{id}/items/{item}', NavigationApiController::class, 'updateItem', 'cms.navigation.item.update', 'PATCH', $write));
        $registry->registerEndpoint('navigation-api', new ApiEndpoint('cms/navigation/{id}/items/{item}', NavigationApiController::class, 'destroyItem', 'cms.navigation.item.delete', 'DELETE', $write));
    }
}
