<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditorApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BlockEditorApi\Http\BlockEditorController;

final class BlockEditorApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/block-editor')->middleware('api')->group(function (): void {
            Route::put('documents/{subjectType}/{subjectId}', [BlockEditorController::class, 'save'])->name('cms.block-editor.documents.save');
            Route::post('documents/{document}/lock', [BlockEditorController::class, 'lock'])->name('cms.block-editor.documents.lock');
            Route::post('patterns', [BlockEditorController::class, 'pattern'])->name('cms.block-editor.patterns.create');
        });
    }
}
