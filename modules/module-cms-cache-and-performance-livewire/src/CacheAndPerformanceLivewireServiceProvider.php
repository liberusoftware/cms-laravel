<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\CacheAndPerformanceLivewire\Livewire\CacheDiagnostics;
use Livewire\Livewire;

final class CacheAndPerformanceLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-cache-and-performance-livewire');
        Livewire::component('module-cms-cache-and-performance.diagnostics', CacheDiagnostics::class);
    }
}
