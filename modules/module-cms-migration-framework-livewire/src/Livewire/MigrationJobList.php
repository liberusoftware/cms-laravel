<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFrameworkLivewire\Livewire;

use Liberu\Cms\MigrationFramework\Models\MigrationJob;
use Livewire\Component;

final class MigrationJobList extends Component
{
    public function render(): mixed
    {
        return view('module-cms-migration-framework::job-list', ['jobs' => MigrationJob::query()->latest()->limit(50)->get()]);
    }
}
