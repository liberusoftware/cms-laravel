<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\MultisiteLivewire\Livewire\SiteNetwork;
use Livewire\Livewire;

final class MultisiteLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-multisite-livewire');
        Livewire::component('module-cms-multisite.site-network', SiteNetwork::class);
    }
}
