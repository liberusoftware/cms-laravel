<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContentApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\RelatedContentApi\Http\RelatedContentController;

final class RelatedContentApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('related-content-api', new ApiEndpoint('cms/related-content/{type}/{id}', RelatedContentController::class, 'index', 'cms.related-content.index'));
        }
    }
}
