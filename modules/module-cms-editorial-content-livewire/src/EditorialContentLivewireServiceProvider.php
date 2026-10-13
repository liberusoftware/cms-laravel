<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class EditorialContentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-editorial-content-livewire');
        Livewire::addNamespace('module-cms-editorial-content', classNamespace: 'Liberu\\Cms\\EditorialContentLivewire\\Livewire');
    }
}
