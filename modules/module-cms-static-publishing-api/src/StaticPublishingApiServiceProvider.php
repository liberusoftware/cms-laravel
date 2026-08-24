<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\StaticPublishingApi\Http\StaticPublishingController;

final class StaticPublishingApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('static-publishing-api', new ApiEndpoint('cms/static-publishing/builds', StaticPublishingController::class, 'store', 'cms.static-publishing.store', 'POST'));
        }
    }
}
