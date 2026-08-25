<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ViewsAndQueryBuilderApi\Http\Controllers\ViewsAndQueryBuilderController;

final class ViewsAndQueryBuilderApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('views-and-query-builder-api', new ApiEndpoint('cms/views-and-query-builder', ViewsAndQueryBuilderController::class, 'index', 'cms.views.index'));
        $registry->registerEndpoint('views-and-query-builder-api', new ApiEndpoint('cms/views-and-query-builder/{slug}', ViewsAndQueryBuilderController::class, 'show', 'cms.views.show'));
        $registry->registerEndpoint('views-and-query-builder-api', new ApiEndpoint('cms/views-and-query-builder/{slug}/execute', ViewsAndQueryBuilderController::class, 'execute', 'cms.views.execute'));
    }
}
