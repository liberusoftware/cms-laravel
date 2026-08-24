<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\RedirectsApi\Http\RedirectController;

final class RedirectsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('redirects-resolve-api', new ApiEndpoint('cms/redirects/resolve', RedirectController::class, 'resolve', 'cms.redirects.resolve'));
        $registry->registerEndpoint('redirects-create-api', new ApiEndpoint('cms/redirects', RedirectController::class, 'store', 'cms.redirects.store', 'POST'));
    }
}
