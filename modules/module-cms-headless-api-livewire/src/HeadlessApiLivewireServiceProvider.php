<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class HeadlessApiLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-headless-api-livewire');
        Livewire::addNamespace('module-cms-headless-api', classNamespace: 'Liberu\\Cms\\HeadlessApiLivewire\\Livewire');
    }
}
