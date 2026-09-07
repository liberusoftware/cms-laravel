<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ImageProcessingLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-image-processing-livewire');
        Livewire::addNamespace('module-cms-image-processing', classNamespace: 'Liberu\\Cms\\ImageProcessingLivewire\\Livewire');
    }
}
