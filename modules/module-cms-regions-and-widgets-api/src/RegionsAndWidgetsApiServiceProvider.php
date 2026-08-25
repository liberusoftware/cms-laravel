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
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('regions-and-widgets-api', new ApiEndpoint('cms/regions-and-widgets/{key}', RegionsAndWidgetsController::class, 'show', 'cms.regions-and-widgets.show'));
            $r->registerEndpoint('regions-and-widgets-api', new ApiEndpoint('cms/regions-and-widgets/regions', RegionsAndWidgetsController::class, 'region', 'cms.regions-and-widgets.region', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('regions-and-widgets-api', new ApiEndpoint('cms/regions-and-widgets/widgets', RegionsAndWidgetsController::class, 'widget', 'cms.regions-and-widgets.widget', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('regions-and-widgets-api', new ApiEndpoint('cms/regions-and-widgets/{key}/placements', RegionsAndWidgetsController::class, 'place', 'cms.regions-and-widgets.place', 'POST', ['abilities:content:write']));
        }
    }
}
