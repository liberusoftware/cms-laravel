<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentFederationApi\Http\ContentFederationController;

final class ContentFederationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-federation')->middleware('api')->group(function (): void {
            Route::get('sources', [ContentFederationController::class, 'index'])->name('cms.content-federation.sources.index');
            Route::post('sources', [ContentFederationController::class, 'store'])->name('cms.content-federation.sources.store');
            Route::post('sources/{source}/references', [ContentFederationController::class, 'ingest'])->name('cms.content-federation.references.store');
            Route::get('sources/{source}/references/{type}/{key}', [ContentFederationController::class, 'fallback'])->name('cms.content-federation.references.show');
        });
    }
}
