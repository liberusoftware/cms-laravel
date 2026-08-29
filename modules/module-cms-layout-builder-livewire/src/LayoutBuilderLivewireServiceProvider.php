<?php

declare(strict_types=1);

namespace Liberu\Cms\LayoutBuilderLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\LayoutBuilderLivewire\Livewire\LayoutBrowser;
use Livewire\Livewire;

final class LayoutBuilderLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-layout-builder');
        Livewire::component('module-cms-layout-builder::layout-browser', LayoutBrowser::class);
    }
}
