<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\AnalyticsIntegrationLivewire\Livewire\AnalyticsDashboard;
use Livewire\Livewire;

final class AnalyticsIntegrationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-analytics-integration-livewire');
        Livewire::component('module-cms-analytics-integration.dashboard', AnalyticsDashboard::class);
    }
}
