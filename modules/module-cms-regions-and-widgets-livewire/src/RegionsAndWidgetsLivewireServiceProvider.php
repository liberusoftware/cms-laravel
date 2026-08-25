<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\RegionsAndWidgetsLivewire\Livewire\RegionRenderer;
use Livewire\Livewire;

final class RegionsAndWidgetsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-regions-and-widgets.region-renderer', RegionRenderer::class);
    }
}
