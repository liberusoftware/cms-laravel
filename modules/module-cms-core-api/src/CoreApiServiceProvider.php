<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\CoreApi\Http\Controllers\CoreApiController;

final class CoreApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('core', new ApiEndpoint('sites', CoreApiController::class, 'sites', 'core.sites'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}', CoreApiController::class, 'site', 'core.site'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/channels', CoreApiController::class, 'channels', 'core.channels'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/aliases/{alias}', CoreApiController::class, 'alias', 'core.alias'));
        }
    }

    public function boot(): void {}
}
