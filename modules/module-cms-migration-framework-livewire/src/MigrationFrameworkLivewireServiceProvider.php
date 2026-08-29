<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFrameworkLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\MigrationFrameworkLivewire\Livewire\MigrationJobList;
use Livewire\Livewire;

final class MigrationFrameworkLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-migration-framework');
        Livewire::component('module-cms-migration-framework::job-list', MigrationJobList::class);
    }
}
