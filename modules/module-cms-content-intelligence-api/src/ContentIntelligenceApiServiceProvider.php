<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntelligenceApi\Http\ContentIntelligenceController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class ContentIntelligenceApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('content-intelligence-api', new ApiEndpoint('cms/content-intelligence/insights', ContentIntelligenceController::class, 'index', 'cms.content-intelligence.insights.index'));
        $registry->registerEndpoint('content-intelligence-api', new ApiEndpoint('cms/content-intelligence/insights', ContentIntelligenceController::class, 'store', 'cms.content-intelligence.insights.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('content-intelligence-api', new ApiEndpoint('cms/content-intelligence/insights/{insight}/review', ContentIntelligenceController::class, 'review', 'cms.content-intelligence.insights.review', 'POST', ['abilities:content:write']));
    }
}
