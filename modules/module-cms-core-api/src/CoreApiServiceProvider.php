<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\Core\Queries\CoreQueryService;
use Liberu\Cms\CoreApi\Http\Controllers\CoreApiController;

final class CoreApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CoreQueryService::class);
        $this->app->singleton(CoreMutationService::class);

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('core', new ApiEndpoint('sites', CoreApiController::class, 'sites', 'core.sites'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites', CoreApiController::class, 'createSite', 'core.sites.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}', CoreApiController::class, 'site', 'core.site'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/channels', CoreApiController::class, 'channels', 'core.channels'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/channels', CoreApiController::class, 'createChannel', 'core.channels.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/aliases/{alias}', CoreApiController::class, 'alias', 'core.alias'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/aliases', CoreApiController::class, 'aliases', 'core.aliases'));
            $registry->registerEndpoint('core', new ApiEndpoint('sites/{site}/identities', CoreApiController::class, 'identities', 'core.identities'));
        }
    }
}
