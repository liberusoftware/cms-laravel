<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Core\Queries\CoreQueryService;
use Livewire\Livewire;

final class CoreLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-core-livewire');
        Livewire::addNamespace(
            'module-cms-core',
            classNamespace: 'Liberu\\Cms\\CoreLivewire\\Livewire',
        );
    }

    public function register(): void
    {
        $this->app->singleton(CoreQueryService::class);
    }
}
