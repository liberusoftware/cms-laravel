<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\PublishingLivewire\Livewire\ReleaseMonitor;
use Livewire\Livewire;

final class PublishingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-publishing.release-monitor', ReleaseMonitor::class);
    }
}
