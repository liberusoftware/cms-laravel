<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTemplatesLivewire\Livewire\TemplatePicker;
use Livewire\Livewire;

final class ContentTemplatesLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-content-templates');
        Livewire::component('module-cms-content-templates::template-picker', TemplatePicker::class);
    }
}
