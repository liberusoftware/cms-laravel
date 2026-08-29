<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\RecommendationsApi\Http\RecommendationController;

final class RecommendationsApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $r = $this->app->make(ApiResourceRegistryInterface::class);
            $r->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations', RecommendationController::class, 'createList', 'cms.recommendations.create', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations/{key}', RecommendationController::class, 'index', 'cms.recommendations.index'));
            $r->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations/{key}', RecommendationController::class, 'update', 'cms.recommendations.update', 'PATCH', ['abilities:content:write']));
            $r->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations/{key}', RecommendationController::class, 'remove', 'cms.recommendations.remove', 'DELETE', ['abilities:content:write']));
            $r->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations/{key}/items', RecommendationController::class, 'addItem', 'cms.recommendations.item', 'POST', ['abilities:content:write']));
            $r->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations/{key}/exclude', RecommendationController::class, 'exclude', 'cms.recommendations.exclude', 'POST', ['abilities:content:write']));
        }
    }
}
