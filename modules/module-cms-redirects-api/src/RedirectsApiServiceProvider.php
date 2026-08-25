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
        $registry->registerEndpoint('redirects-create-api', new ApiEndpoint('cms/redirects', RedirectController::class, 'store', 'cms.redirects.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('redirects-api', new ApiEndpoint('cms/redirects/import', RedirectController::class, 'import', 'cms.redirects.import', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('redirects-api', new ApiEndpoint('cms/redirects/slug-change', RedirectController::class, 'slugChange', 'cms.redirects.slug-change', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('redirects-api', new ApiEndpoint('cms/redirects/suggestions', RedirectController::class, 'suggestions', 'cms.redirects.suggestions'));
    }
}
