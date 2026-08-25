<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentLockingApi\Http\ContentLockingController;

final class ContentLockingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-locking')->middleware('api')->group(function (): void {
            Route::post('locks', [ContentLockingController::class, 'acquire'])->name('cms.content-locking.locks.acquire');
            Route::post('locks/{lock}/renew', [ContentLockingController::class, 'renew'])->name('cms.content-locking.locks.renew');
            Route::post('locks/{lock}/compare', [ContentLockingController::class, 'compare'])->name('cms.content-locking.locks.compare');
            Route::delete('locks/{lock}', [ContentLockingController::class, 'release'])->name('cms.content-locking.locks.release');
        });
    }
}
