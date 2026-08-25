<?php

declare(strict_types=1);

namespace Liberu\Cms\PagesApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Pages\Http\Controllers\PageApiController;
use Liberu\Cms\Pages\Http\Controllers\PageWriteController;
use Liberu\Cms\PagesApi\Http\Controllers\PageRoutingController;

final class PagesApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages', PageApiController::class, 'index', 'cms.pages.list'));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages/{slug}', PageApiController::class, 'show', 'cms.pages.get'));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages', PageWriteController::class, 'store', 'cms.pages.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages/{id}', PageWriteController::class, 'update', 'cms.pages.update', 'PUT', ['abilities:content:write']));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages/{id}', PageWriteController::class, 'destroy', 'cms.pages.delete', 'DELETE', ['abilities:content:write']));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/pages/{id}/aliases', PageRoutingController::class, 'alias', 'cms.pages.alias', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/page-redirects', PageRoutingController::class, 'redirect', 'cms.pages.redirect.create', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('pages-api', new ApiEndpoint('cms/page-redirects/{id}', PageRoutingController::class, 'deleteRedirect', 'cms.pages.redirect.delete', 'DELETE', ['abilities:content:write']));
        }
    }
}
