<?php

declare(strict_types=1);

namespace Liberu\Cms\AnalyticsIntegrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\AnalyticsIntegration\Services\AnalyticsIntegrationService;
use Livewire\Component;

final class AnalyticsDashboard extends Component
{
    /** @var array{total:int,by_type:array<string,int>,accepted:int,suppressed:int} */
    public array $summary = ['total' => 0, 'by_type' => [], 'accepted' => 0, 'suppressed' => 0];

    public function refreshDashboard(AnalyticsIntegrationService $service): void
    {
        $this->summary = $service->dashboard(null);
    }

    public function render(): View
    {
        return view('module-cms-analytics-integration-livewire::dashboard');
    }
}
