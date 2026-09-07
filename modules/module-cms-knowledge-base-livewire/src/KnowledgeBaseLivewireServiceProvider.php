<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseLivewire;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class KnowledgeBaseLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-knowledge-base-livewire');
        Livewire::addNamespace('module-cms-knowledge-base', classNamespace: 'Liberu\\Cms\\KnowledgeBaseLivewire\\Livewire');
    }
}
