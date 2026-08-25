<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DisplayModesApi\Http\DisplayModesController;

final class DisplayModesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/display-modes')->middleware('api')->group(function (): void {
            Route::get('modes', [DisplayModesController::class, 'index'])->name('cms.display-modes.index');
            Route::post('modes', [DisplayModesController::class, 'store'])->name('cms.display-modes.store');
            Route::get('projection', [DisplayModesController::class, 'select'])->name('cms.display-modes.select');
        });
    }
}
