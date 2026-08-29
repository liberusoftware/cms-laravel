<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class EventsContentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-events-content-livewire');
        Livewire::addNamespace('module-cms-events-content', classNamespace: 'Liberu\\Cms\\EventsContentLivewire\\Livewire');
    }
}
