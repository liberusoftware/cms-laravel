<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentSearchApi\Http\ContentSearchController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentSearchApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-search-api', new ApiEndpoint('cms/content-search/search', ContentSearchController::class, 'search', 'cms.content-search.search'));
        $registry->registerEndpoint('content-search-api', new ApiEndpoint('cms/content-search/autocomplete', ContentSearchController::class, 'autocomplete', 'cms.content-search.autocomplete'));
        $registry->registerEndpoint('content-search-api', new ApiEndpoint('cms/content-search/analytics', ContentSearchController::class, 'analytics', 'cms.content-search.analytics'));
    }
}
