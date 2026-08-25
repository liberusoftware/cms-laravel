<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\WordPressMigrationLivewire\Livewire\MigrationList;
use Livewire\Livewire;

final class WordPressMigrationLivewireServiceProvider extends ServiceProvider { public function boot(): void { $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-wordpress-migration-livewire'); Livewire::addNamespace('module-cms-wordpress-migration', classNamespace: 'Liberu\\Cms\\WordPressMigrationLivewire\\Livewire'); Livewire::component('module-cms-wordpress-migration::migration-list', MigrationList::class); } }
