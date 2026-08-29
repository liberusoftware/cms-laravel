<?php

namespace Liberu\Cms\EmbedsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class EmbedsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-embeds-livewire');
        Livewire::addNamespace('module-cms-embeds', classNamespace: 'Liberu\\Cms\\EmbedsLivewire\\Livewire');
    }
}
