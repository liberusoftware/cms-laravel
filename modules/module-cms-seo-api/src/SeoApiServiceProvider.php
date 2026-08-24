<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\SeoApi\Http\SeoController;

final class SeoApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('seo-api', new ApiEndpoint('cms/seo/{type}/{id}', SeoController::class, 'show', 'cms.seo.show'));
        }
    }
}
