<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Liberu\Cms\ViewsAndQueryBuilderLivewire\Livewire\ViewBrowser;

final class ViewsAndQueryBuilderLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-views-and-query-builder-livewire');
        Livewire::addNamespace('module-cms-views-and-query-builder', classNamespace: 'Liberu\\Cms\\ViewsAndQueryBuilderLivewire\\Livewire');
    }
}
