<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class LocalizationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-localization-livewire');
        Livewire::addNamespace('module-cms-localization', classNamespace: 'Liberu\\Cms\\LocalizationLivewire\\Livewire');
    }
}
