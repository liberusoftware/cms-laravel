<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ForumsIntegrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-forums-integration-livewire');
        Livewire::addNamespace('module-cms-forums-integration', classNamespace: 'Liberu\\Cms\\ForumsIntegrationLivewire\\Livewire');
    }
}
