<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Livewire\Livewire;

final class ContentEntitiesLivewireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PublishedEntityQuery::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-content-entities-livewire');
        Livewire::addNamespace(
            'module-cms-content-entities',
            classNamespace: 'Liberu\\Cms\\ContentEntitiesLivewire\\Livewire',
        );
    }
}
