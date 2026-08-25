<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ThemeMarketplaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-theme-marketplace-livewire');
        Livewire::addNamespace('module-cms-theme-marketplace', classNamespace: 'Liberu\\Cms\\ThemeMarketplaceLivewire\\Livewire');
    }
}
