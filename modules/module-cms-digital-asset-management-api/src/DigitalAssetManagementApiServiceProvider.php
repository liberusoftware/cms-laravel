<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagementApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DigitalAssetManagementApi\Http\DigitalAssetController;

final class DigitalAssetManagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/digital-asset-management')->middleware('api')->group(function (): void {
            Route::get('assets', [DigitalAssetController::class, 'index'])->name('cms.digital-assets.index');
            Route::post('assets', [DigitalAssetController::class, 'store'])->name('cms.digital-assets.store');
            Route::post('assets/{asset}/approve', [DigitalAssetController::class, 'approve'])->name('cms.digital-assets.approve');
            Route::post('assets/{asset}/renditions', [DigitalAssetController::class, 'rendition'])->name('cms.digital-assets.renditions.store');
            Route::post('assets/{asset}/distribution', [DigitalAssetController::class, 'distribute'])->name('cms.digital-assets.distribute');
        });
    }
}
