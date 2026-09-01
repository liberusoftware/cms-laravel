<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BackupAndRestoreApi\Http\BackupAndRestoreController;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;

final class BackupAndRestoreApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('backup-and-restore-api', new ApiEndpoint('cms/backup-and-restore/artifacts', BackupAndRestoreController::class, 'create', 'cms.backup-and-restore.artifacts.create', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('backup-and-restore-api', new ApiEndpoint('cms/backup-and-restore/artifacts/{artifact}/verify', BackupAndRestoreController::class, 'verify', 'cms.backup-and-restore.artifacts.verify', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('backup-and-restore-api', new ApiEndpoint('cms/backup-and-restore/artifacts/{artifact}/restore-preview', BackupAndRestoreController::class, 'preview', 'cms.backup-and-restore.artifacts.preview'));
        $registry->registerEndpoint('backup-and-restore-api', new ApiEndpoint('cms/backup-and-restore/schedules', BackupAndRestoreController::class, 'schedule', 'cms.backup-and-restore.schedules.create', 'POST', ['abilities:content:write']));
    }
}
