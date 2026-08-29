<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ExtensionMarketplaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-extension-marketplace-livewire');
        Livewire::addNamespace('module-cms-extension-marketplace', classNamespace: 'Liberu\\Cms\\ExtensionMarketplaceLivewire\\Livewire');
    }
}
