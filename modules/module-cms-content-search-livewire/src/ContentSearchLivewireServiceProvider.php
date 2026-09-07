<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentSearchLivewire\Livewire\SearchBox;
use Livewire\Livewire;

final class ContentSearchLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-content-search');
        Livewire::component('module-cms-content-search::search-box', SearchBox::class);
    }
}
