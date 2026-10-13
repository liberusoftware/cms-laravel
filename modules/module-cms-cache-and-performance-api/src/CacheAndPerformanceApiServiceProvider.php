<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CacheAndPerformanceApi\Http\CacheAndPerformanceController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class CacheAndPerformanceApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('cache-and-performance-api', new ApiEndpoint('cms/cache-and-performance/remember', CacheAndPerformanceController::class, 'remember', 'cms.cache-and-performance.remember', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('cache-and-performance-api', new ApiEndpoint('cms/cache-and-performance/invalidate', CacheAndPerformanceController::class, 'invalidate', 'cms.cache-and-performance.invalidate', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('cache-and-performance-api', new ApiEndpoint('cms/cache-and-performance/diagnostics', CacheAndPerformanceController::class, 'diagnostics', 'cms.cache-and-performance.diagnostics'));
    }
}
