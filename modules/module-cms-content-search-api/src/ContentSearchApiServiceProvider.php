<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentSearchApi\Http\ContentSearchController;

final class ContentSearchApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-search')->middleware('api')->group(function (): void {
            Route::get('search', [ContentSearchController::class, 'search'])->name('cms.content-search.search');
            Route::get('autocomplete', [ContentSearchController::class, 'autocomplete'])->name('cms.content-search.autocomplete');
            Route::get('analytics', [ContentSearchController::class, 'analytics'])->name('cms.content-search.analytics');
        });
    }
}
