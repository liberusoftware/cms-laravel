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
            $this->app->make(ApiResourceRegistryInterface::class)->registerEndpoint('recommendations-api', new ApiEndpoint('cms/recommendations/{key}', RecommendationController::class, 'index', 'cms.recommendations.index'));
        }
    }
}
