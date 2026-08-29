<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AnalyticsIntegrationApi\Http\AnalyticsIntegrationController;

final class AnalyticsIntegrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/analytics-integration')->middleware('api')->group(function (): void {
            Route::post('events', [AnalyticsIntegrationController::class, 'record'])->name('cms.analytics-integration.events.record');
            Route::post('mappings', [AnalyticsIntegrationController::class, 'mapping'])->name('cms.analytics-integration.mappings.create');
            Route::get('dashboard', [AnalyticsIntegrationController::class, 'dashboard'])->name('cms.analytics-integration.dashboard');
        });
    }
}
