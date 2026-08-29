<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CopilotLivewire\Livewire\CopilotConsole;
use Livewire\Livewire;

final class CopilotLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-copilot-livewire');
        Livewire::component('module-cms-copilot.console', CopilotConsole::class);
    }
}
