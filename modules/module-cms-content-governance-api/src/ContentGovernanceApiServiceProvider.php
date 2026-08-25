<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentGovernanceApi\Http\ContentGovernanceController;

final class ContentGovernanceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-governance')->middleware('api')->group(function (): void {
            Route::get('records', [ContentGovernanceController::class, 'index'])->name('cms.content-governance.records.index');
            Route::post('records', [ContentGovernanceController::class, 'store'])->name('cms.content-governance.records.store');
            Route::post('records/{record}/legal-hold', [ContentGovernanceController::class, 'hold'])->name('cms.content-governance.records.hold');
            Route::post('records/{record}/evidence', [ContentGovernanceController::class, 'evidence'])->name('cms.content-governance.records.evidence');
        });
    }
}
