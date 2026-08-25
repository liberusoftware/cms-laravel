<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentCalendarApi\Http\ContentCalendarController;

final class ContentCalendarApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-calendar')->middleware('api')->group(function (): void {
            Route::get('items', [ContentCalendarController::class, 'index'])->name('cms.content-calendar.items.index');
            Route::post('campaigns', [ContentCalendarController::class, 'campaign'])->name('cms.content-calendar.campaigns.store');
            Route::post('items', [ContentCalendarController::class, 'store'])->name('cms.content-calendar.items.store');
            Route::patch('items/{item}/schedule', [ContentCalendarController::class, 'reschedule'])->name('cms.content-calendar.items.reschedule');
        });
    }
}
