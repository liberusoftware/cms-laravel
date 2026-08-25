<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class TranslationManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-translation-management-livewire');
        Livewire::addNamespace('module-cms-translation-management', classNamespace: 'Liberu\\Cms\\TranslationManagementLivewire\\Livewire');
    }
}
