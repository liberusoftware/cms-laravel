<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ExperimentationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-experimentation-livewire');
        Livewire::addNamespace('module-cms-experimentation', classNamespace: 'Liberu\\Cms\\ExperimentationLivewire\\Livewire');
    }
}
