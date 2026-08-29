<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CopilotApi\Http\CopilotController;

final class CopilotApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/cms-copilot')->middleware('api')->group(function (): void {
            Route::post('requests', [CopilotController::class, 'submit'])->name('cms.copilot.requests.submit');
            Route::post('requests/{request}/execute', [CopilotController::class, 'execute'])->name('cms.copilot.requests.execute');
            Route::post('requests/{request}/confirm', [CopilotController::class, 'confirm'])->name('cms.copilot.requests.confirm');
        });
    }
}
