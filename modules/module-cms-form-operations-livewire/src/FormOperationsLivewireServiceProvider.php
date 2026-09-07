<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class FormOperationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-form-operations-livewire');
        Livewire::addNamespace('module-cms-form-operations', classNamespace: 'Liberu\\Cms\\FormOperationsLivewire\\Livewire');
    }
}
