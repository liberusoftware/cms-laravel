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
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('related-content-api', new ApiEndpoint('cms/related-content/{type}/{id}', RelatedContentController::class, 'index', 'cms.related-content.index'));
            $r->registerEndpoint('related-content-api', new ApiEndpoint('cms/related-content/{type}/{id}/relations', RelatedContentController::class, 'relate', 'cms.related-content.relate', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('related-content-api', new ApiEndpoint('cms/related-content/{type}/{id}/exclude', RelatedContentController::class, 'exclude', 'cms.related-content.exclude', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('related-content-api', new ApiEndpoint('cms/related-content/{type}/{id}', RelatedContentController::class, 'remove', 'cms.related-content.remove', 'DELETE', ['abilities:content:write']));
        }
    }
}
