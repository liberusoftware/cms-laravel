<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AccessibilityAssistantApi\Http\AccessibilityAssistantController;

final class AccessibilityAssistantApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/accessibility-assistant')->middleware('api')->group(function (): void {
            Route::post('analyze', [AccessibilityAssistantController::class, 'analyze'])->name('cms.accessibility-assistant.analyze');
        });
    }
}
