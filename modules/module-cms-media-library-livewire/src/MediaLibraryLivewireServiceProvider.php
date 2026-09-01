<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\MediaLibraryLivewire\Livewire\MediaLibrary;
use Livewire\Livewire;

final class MediaLibraryLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-media-library-livewire');
        Livewire::component('module-cms-media-library.library', MediaLibrary::class);
    }
}
