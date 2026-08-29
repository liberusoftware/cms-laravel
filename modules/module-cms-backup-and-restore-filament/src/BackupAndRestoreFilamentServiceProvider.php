<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BackupAndRestoreFilament\Resources\BackupArtifactResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class BackupAndRestoreFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('backup-and-restore', BackupArtifactResource::class);
        }
    }
}
