<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\CacheAndPerformance\Services\CacheAndPerformanceService;
use Livewire\Component;

final class CacheDiagnostics extends Component
{
    public array $diagnostics = [];

    public function refreshDiagnostics(CacheAndPerformanceService $service): void
    {
        $this->diagnostics = $service->diagnostics(null);
    }

    public function render(): View
    {
        return view('module-cms-cache-and-performance-livewire::diagnostics');
    }
}
