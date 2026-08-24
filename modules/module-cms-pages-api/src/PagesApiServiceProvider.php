<?php

declare(strict_types=1);

namespace Liberu\Cms\PagesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Pages\Http\Controllers\PageApiController;

final class PagesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages', PageApiController::class, 'index', 'cms.pages.list'));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages/{slug}', PageApiController::class, 'show', 'cms.pages.get'));
        }
    }
}
