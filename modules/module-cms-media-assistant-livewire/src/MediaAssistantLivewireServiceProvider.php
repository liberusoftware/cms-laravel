<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class MediaAssistantLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-media-assistant-livewire');
        Livewire::addNamespace('module-cms-media-assistant', classNamespace: 'Liberu\\Cms\\MediaAssistantLivewire\\Livewire');
    }
}
