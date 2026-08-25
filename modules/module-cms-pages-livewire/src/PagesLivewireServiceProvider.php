<?php

declare(strict_types=1);

namespace Liberu\Cms\PagesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Pages\Queries\PageTreeQuery;
use Liberu\Cms\PagesLivewire\Livewire\PageTree;
use Livewire\Livewire;

final class PagesLivewireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PageTreeQuery::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-pages-livewire');
        Livewire::component('module-cms-pages.page-tree', PageTree::class);
    }
}
