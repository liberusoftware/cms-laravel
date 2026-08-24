<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentEntitiesLivewire\Livewire\EntityBrowser;
use Livewire\Livewire;

final class ContentEntitiesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-content-entities-livewire');
        Livewire::component('module-cms-content-entities.entity-browser', EntityBrowser::class);
    }
}
