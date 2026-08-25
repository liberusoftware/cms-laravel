<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTemplatesApi\Http\ContentTemplatesController;

final class ContentTemplatesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/content-templates')->middleware('api')->group(function (): void {
            Route::get('templates', [ContentTemplatesController::class, 'index'])->name('cms.content-templates.index');
            Route::post('templates', [ContentTemplatesController::class, 'store'])->name('cms.content-templates.store');
            Route::post('templates/{template}/publish', [ContentTemplatesController::class, 'publish'])->name('cms.content-templates.publish');
            Route::post('templates/{template}/lock', [ContentTemplatesController::class, 'lock'])->name('cms.content-templates.lock');
        });
    }
}
