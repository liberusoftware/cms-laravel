<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\IntegrationDirectoryLivewire\Livewire\IntegrationList;
use Livewire\Livewire;

final class IntegrationDirectoryLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-integration-directory');
        Livewire::component('module-cms-integration-directory::integration-list', IntegrationList::class);
    }
}
