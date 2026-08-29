<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreApi;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BackupAndRestoreApi\Http\BackupAndRestoreController;

final class BackupAndRestoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::prefix('api/v1/cms/backup-and-restore')->middleware('api')->group(function (): void {
            Route::post('artifacts', [BackupAndRestoreController::class, 'create'])->name('cms.backup-and-restore.artifacts.create');
            Route::post('artifacts/{artifact}/verify', [BackupAndRestoreController::class, 'verify'])->name('cms.backup-and-restore.artifacts.verify');
            Route::get('artifacts/{artifact}/restore-preview', [BackupAndRestoreController::class, 'preview'])->name('cms.backup-and-restore.artifacts.preview');
            Route::post('schedules', [BackupAndRestoreController::class, 'schedule'])->name('cms.backup-and-restore.schedules.create');
        });
    }
}
