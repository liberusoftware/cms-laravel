<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ThemeMarketplaceLivewire\Livewire\ThemeCatalog;
use Livewire\Livewire;

final class ThemeMarketplaceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-theme-marketplace.theme-catalog', ThemeCatalog::class);
    }
}
