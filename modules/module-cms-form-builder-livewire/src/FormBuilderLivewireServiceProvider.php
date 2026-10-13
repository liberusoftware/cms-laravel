<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilderLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class FormBuilderLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-form-builder-livewire');
        Livewire::addNamespace('module-cms-form-builder', classNamespace: 'Liberu\\Cms\\FormBuilderLivewire\\Livewire');
    }
}
