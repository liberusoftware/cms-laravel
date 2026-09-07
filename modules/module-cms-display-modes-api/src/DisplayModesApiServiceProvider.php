<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\DisplayModesApi\Http\DisplayModesController;

final class DisplayModesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('display-modes-api', new ApiEndpoint('cms/display-modes/modes', DisplayModesController::class, 'index', 'cms.display-modes.index'));
        $registry->registerEndpoint('display-modes-api', new ApiEndpoint('cms/display-modes/modes', DisplayModesController::class, 'store', 'cms.display-modes.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('display-modes-api', new ApiEndpoint('cms/display-modes/projection', DisplayModesController::class, 'select', 'cms.display-modes.select'));
    }
}
