<?php

declare(strict_types=1);

namespace Liberu\Cms\CollectionsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CollectionsLivewire\Livewire\CollectionBrowser;
use Livewire\Livewire;

final class CollectionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-collections-livewire');
        Livewire::component('module-cms-collections.collection-browser', CollectionBrowser::class);
    }
}
