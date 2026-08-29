<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AccessibilityAssistantLivewire\Livewire\AccessibilityAnalyzer;
use Livewire\Livewire;

final class AccessibilityAssistantLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-accessibility-assistant-livewire');
        Livewire::component('module-cms-accessibility-assistant.analyzer', AccessibilityAnalyzer::class);
    }
}
