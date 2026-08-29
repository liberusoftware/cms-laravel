<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\BackupAndRestoreLivewire\Livewire\BackupPreview;
use Livewire\Livewire;

final class BackupAndRestoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-backup-and-restore-livewire');
        Livewire::component('module-cms-backup-and-restore.preview', BackupPreview::class);
    }
}
