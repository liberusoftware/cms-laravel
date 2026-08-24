<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CoreLivewire\Livewire\SiteList;
use Livewire\Livewire;

final class CoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-core-livewire');
        Livewire::component('module-cms-core::site-list', SiteList::class);
    }
}
