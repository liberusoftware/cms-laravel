<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntelligenceApi\Http\ContentIntelligenceController;

final class ContentIntelligenceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-intelligence')->middleware('api')->group(function (): void {
            Route::get('insights', [ContentIntelligenceController::class, 'index'])->name('cms.content-intelligence.insights.index');
            Route::post('insights', [ContentIntelligenceController::class, 'store'])->name('cms.content-intelligence.insights.store');
            Route::post('insights/{insight}/review', [ContentIntelligenceController::class, 'review'])->name('cms.content-intelligence.insights.review');
        });
    }
}
