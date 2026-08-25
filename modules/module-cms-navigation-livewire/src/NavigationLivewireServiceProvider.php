<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\NavigationLivewire\Livewire\NavigationMenu;
use Livewire\Livewire;

final class NavigationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-navigation-livewire');
        Livewire::component('module-cms-navigation.menu', NavigationMenu::class);
    }
}
