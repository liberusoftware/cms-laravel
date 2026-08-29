<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigrationLivewire\Livewire;

use Liberu\Cms\MigrationFramework\Models\MigrationJob;
use Livewire\Component;

final class DrupalMigrationList extends Component
{
    public function render(): mixed
    {
        return view('module-cms-drupal-migration::migration-list', ['migrations' => MigrationJob::query()->where('source', 'drupal')->latest()->limit(50)->get()]);
    }
}
