<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\MetadataLivewire\Livewire\MetadataBrowser;
use Livewire\Livewire;

final class MetadataLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-metadata');
        Livewire::component('module-cms-metadata::metadata-browser', MetadataBrowser::class);
    }
}
