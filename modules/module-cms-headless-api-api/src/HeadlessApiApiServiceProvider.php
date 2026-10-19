<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\HeadlessApiApi\Http\PersistedQueryController;

final class HeadlessApiApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('headless-api-api', new ApiEndpoint('cms/headless/persisted-queries', PersistedQueryController::class, 'store', 'cms.headless.persisted-queries.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('headless-api-api', new ApiEndpoint('cms/headless/persisted-queries/{hash}', PersistedQueryController::class, 'resolve', 'cms.headless.persisted-queries.resolve'));
    }
}
