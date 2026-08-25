<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\RegionsAndWidgets\Services\RegionWidgetService;
use Livewire\Component;

final class RegionRenderer extends Component
{
    public string $regionKey = '';

    public array $context = [];

    public function render(RegionWidgetService $service): View
    {
        return view('cms-regions-and-widgets-livewire::region-renderer', ['widgets' => $this->regionKey === '' ? [] : $service->render($this->regionKey, $this->context)]);
    }
}
