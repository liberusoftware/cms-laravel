<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\JoomlaMigrationLivewire\Livewire\JoomlaMigrationList;
use Livewire\Livewire;

final class JoomlaMigrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-joomla-migration');
        Livewire::component('module-cms-joomla-migration::migration-list', JoomlaMigrationList::class);
    }
}
