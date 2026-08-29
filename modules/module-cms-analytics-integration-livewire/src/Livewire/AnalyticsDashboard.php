<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\AnalyticsIntegration\Services\AnalyticsIntegrationService;
use Livewire\Component;

final class AnalyticsDashboard extends Component
{
    public array $summary = [];

    public function refreshDashboard(AnalyticsIntegrationService $service): void
    {
        $this->summary = $service->dashboard(null);
    }

    public function render(): View
    {
        return view('module-cms-analytics-integration-livewire::dashboard');
    }
}
