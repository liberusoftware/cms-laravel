<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DrupalMigrationLivewire\Livewire\DrupalMigrationList;
use Livewire\Livewire;

final class DrupalMigrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-drupal-migration');
        Livewire::component('module-cms-drupal-migration::migration-list', DrupalMigrationList::class);
    }
}
