<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\StaticPublishingLivewire\Livewire\BuildMonitor;
use Livewire\Livewire;

final class StaticPublishingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::addNamespace('module-cms-static-publishing', classNamespace: 'Liberu\\Cms\\StaticPublishingLivewire\\Livewire');
        Livewire::component('module-cms-static-publishing::build-monitor', BuildMonitor::class);
    }
}
