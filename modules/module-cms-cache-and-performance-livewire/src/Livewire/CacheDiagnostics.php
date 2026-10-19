<?php

declare(strict_types=1);

namespace Liberu\Cms\CacheAndPerformanceLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\CacheAndPerformance\Services\CacheAndPerformanceService;
use Livewire\Component;

final class CacheDiagnostics extends Component
{
    /** @var array{entries:int,warm:int,hits:int,misses:int,hit_rate:float} */
    public array $diagnostics = ['entries' => 0, 'warm' => 0, 'hits' => 0, 'misses' => 0, 'hit_rate' => 0.0];

    public function refreshDiagnostics(CacheAndPerformanceService $service): void
    {
        $this->diagnostics = $service->diagnostics(null);
    }

    public function render(): View
    {
        return view('module-cms-cache-and-performance-livewire::diagnostics');
    }
}
