<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManagerLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ExtensionManagerLivewire\Livewire\ExtensionList;
use Livewire\Livewire;

final class ExtensionManagerLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-extension-manager');
        Livewire::component('module-cms-extension-manager::extension-list', ExtensionList::class);
    }
}
