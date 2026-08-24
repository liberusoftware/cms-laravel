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
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('sitemaps-api', new ApiEndpoint('cms/sitemaps', SitemapsController::class, 'index', 'cms.sitemaps.index'));
        }
    }
}
