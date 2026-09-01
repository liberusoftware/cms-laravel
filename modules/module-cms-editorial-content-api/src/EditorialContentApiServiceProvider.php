<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\EditorialContentApi\Http\EditorialContentController;

final class EditorialContentApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('editorial-content-api', new ApiEndpoint('cms/editorial-content', EditorialContentController::class, 'index', 'cms.editorial-content.index'));
        $registry->registerEndpoint('editorial-content-api', new ApiEndpoint('cms/editorial-content/{key}', EditorialContentController::class, 'show', 'cms.editorial-content.show'));
        $registry->registerEndpoint('editorial-content-api', new ApiEndpoint('cms/editorial-content', EditorialContentController::class, 'store', 'cms.editorial-content.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-content-api', new ApiEndpoint('cms/editorial-content/{key}/publish', EditorialContentController::class, 'publish', 'cms.editorial-content.publish', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('editorial-content-api', new ApiEndpoint('cms/editorial-content/{key}/archive', EditorialContentController::class, 'archive', 'cms.editorial-content.archive', 'POST', ['abilities:content:write']));
    }
}
