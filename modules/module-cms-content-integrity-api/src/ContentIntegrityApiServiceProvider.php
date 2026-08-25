<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntegrityApi\Http\ContentIntegrityController;

final class ContentIntegrityApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-integrity')->middleware('api')->group(function (): void {
            Route::get('findings', [ContentIntegrityController::class, 'index'])->name('cms.content-integrity.findings.index');
            Route::post('scans', [ContentIntegrityController::class, 'scan'])->name('cms.content-integrity.scans.store');
            Route::post('scans/{scan}/findings', [ContentIntegrityController::class, 'finding'])->name('cms.content-integrity.findings.store');
            Route::post('findings/{finding}/resolve', [ContentIntegrityController::class, 'resolve'])->name('cms.content-integrity.findings.resolve');
        });
    }
}
