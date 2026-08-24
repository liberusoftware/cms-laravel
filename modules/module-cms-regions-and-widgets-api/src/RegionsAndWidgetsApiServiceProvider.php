<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\RegionsAndWidgetsApi\Http\RegionsAndWidgetsController;

final class RegionsAndWidgetsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('regions-and-widgets-api', new ApiEndpoint('cms/regions-and-widgets/{key}', RegionsAndWidgetsController::class, 'show', 'cms.regions-and-widgets.show'));
        }
    }
}
