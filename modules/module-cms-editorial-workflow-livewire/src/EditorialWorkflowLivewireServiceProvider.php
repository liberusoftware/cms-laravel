<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class EditorialWorkflowLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-editorial-workflow-livewire');
        Livewire::addNamespace('module-cms-editorial-workflow', classNamespace: 'Liberu\\Cms\\EditorialWorkflowLivewire\\Livewire');
    }
}
