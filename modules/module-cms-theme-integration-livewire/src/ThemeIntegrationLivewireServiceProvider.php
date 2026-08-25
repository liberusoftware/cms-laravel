<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ThemeIntegrationLivewire\Livewire\ThemePreview;
use Livewire\Livewire;

final class ThemeIntegrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::addNamespace('module-cms-theme-integration', classNamespace: 'Liberu\\Cms\\ThemeIntegrationLivewire\\Livewire');
        Livewire::component('module-cms-theme-integration::theme-preview', ThemePreview::class);
    }
}
