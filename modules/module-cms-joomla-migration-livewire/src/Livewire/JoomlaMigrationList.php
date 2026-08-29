<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigrationLivewire\Livewire;

use Liberu\Cms\MigrationFramework\Models\MigrationJob;
use Livewire\Component;

final class JoomlaMigrationList extends Component
{
    public function render(): mixed
    {
        return view('module-cms-joomla-migration::migration-list', ['migrations' => MigrationJob::query()->where('source', 'joomla')->latest()->limit(50)->get()]);
    }
}
