<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentEntitiesApi\Http\Controllers\ContentEntitiesController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentEntitiesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint(
                'content-entities',
                new ApiEndpoint('entities/{type}', ContentEntitiesController::class, 'index', 'entities.index'),
            );
        }
    }
}
