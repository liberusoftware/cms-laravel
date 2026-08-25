<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DisplayModesLivewire\Livewire\ModePicker;
use Livewire\Livewire;

final class DisplayModesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-display-modes');
        Livewire::component('module-cms-display-modes::mode-picker', ModePicker::class);
    }
}
