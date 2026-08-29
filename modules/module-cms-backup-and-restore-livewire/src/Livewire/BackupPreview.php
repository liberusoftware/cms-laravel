<?php

declare(strict_types=1);

namespace Liberu\Cms\BackupAndRestoreLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\BackupAndRestore\Models\BackupArtifact;
use Liberu\Cms\BackupAndRestore\Services\BackupAndRestoreService;
use Livewire\Component;

final class BackupPreview extends Component
{
    public ?int $artifactId = null;

    public array $preview = [];

    public function loadPreview(BackupAndRestoreService $service): void
    {
        $this->preview = $this->artifactId === null ? [] : $service->restorePreview(BackupArtifact::query()->findOrFail($this->artifactId));
    }

    public function render(): View
    {
        return view('module-cms-backup-and-restore-livewire::preview');
    }
}
