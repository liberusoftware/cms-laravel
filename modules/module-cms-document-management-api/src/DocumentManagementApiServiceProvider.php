<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DocumentManagementApi\Http\DocumentsController;

final class DocumentManagementApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/documents')->middleware('api')->group(function (): void {
            Route::get('/', [DocumentsController::class, 'index']);
            Route::post('/', [DocumentsController::class, 'store']);
            Route::post('{document}/status', [DocumentsController::class, 'status']);
        });
    }
}
