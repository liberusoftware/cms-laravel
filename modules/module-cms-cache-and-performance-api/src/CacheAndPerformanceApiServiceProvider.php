<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CacheAndPerformanceApi\Http\CacheAndPerformanceController;

final class CacheAndPerformanceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/cache-and-performance')->middleware('api')->group(function (): void {
            Route::post('remember', [CacheAndPerformanceController::class, 'remember'])->name('cms.cache-and-performance.remember');
            Route::post('invalidate', [CacheAndPerformanceController::class, 'invalidate'])->name('cms.cache-and-performance.invalidate');
            Route::get('diagnostics', [CacheAndPerformanceController::class, 'diagnostics'])->name('cms.cache-and-performance.diagnostics');
        });
    }
}
