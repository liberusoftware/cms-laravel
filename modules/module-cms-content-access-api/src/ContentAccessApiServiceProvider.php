<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentAccessApi\Http\ContentAccessController;

final class ContentAccessApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-access')->middleware('api')->group(function (): void {
            Route::post('rules', [ContentAccessController::class, 'store'])->name('cms.content-access.rules.store');
            Route::post('check', [ContentAccessController::class, 'check'])->name('cms.content-access.check');
            Route::post('private-links', [ContentAccessController::class, 'privateLink'])->name('cms.content-access.private-links.store');
        });
    }
}
