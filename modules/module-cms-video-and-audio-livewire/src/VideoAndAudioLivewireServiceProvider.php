<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class VideoAndAudioLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-video-and-audio-livewire');
        Livewire::addNamespace('module-cms-video-and-audio', classNamespace: 'Liberu\\Cms\\VideoAndAudioLivewire\\Livewire');
    }
}
