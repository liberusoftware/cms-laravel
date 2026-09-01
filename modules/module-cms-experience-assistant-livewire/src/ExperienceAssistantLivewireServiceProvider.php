<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class ExperienceAssistantLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-experience-assistant-livewire');
        Livewire::addNamespace('module-cms-experience-assistant', classNamespace: 'Liberu\\Cms\\ExperienceAssistantLivewire\\Livewire');
    }
}
