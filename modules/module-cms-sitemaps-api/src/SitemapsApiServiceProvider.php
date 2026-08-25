<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SitemapsApi\Http\SitemapsController;

final class SitemapsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('sitemaps-api', new ApiEndpoint('cms/sitemaps', SitemapsController::class, 'index', 'cms.sitemaps.index'));
            $r->registerEndpoint('sitemaps-api', new ApiEndpoint('cms/sitemaps', SitemapsController::class, 'create', 'cms.sitemaps.create', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('sitemaps-api', new ApiEndpoint('cms/sitemaps/exclude', SitemapsController::class, 'exclude', 'cms.sitemaps.exclude', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('sitemaps-api', new ApiEndpoint('cms/sitemaps/notify', SitemapsController::class, 'notify', 'cms.sitemaps.notify', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('sitemaps-api', new ApiEndpoint('cms/sitemaps/chunks', SitemapsController::class, 'chunks', 'cms.sitemaps.chunks'));
        }
    }
}
