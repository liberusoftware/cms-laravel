<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/** The legacy collections Livewire provider remains the single component owner. */
final class StructuredCollectionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-structured-collections-livewire');
        Livewire::addNamespace('module-cms-structured-collections', classNamespace: 'Liberu\\Cms\\StructuredCollectionsLivewire\\Livewire');
    }
}
