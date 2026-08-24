<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\StaticPublishing\Models\StaticBuild;
use Livewire\Component;

final class BuildMonitor extends Component
{
    public function render(): View
    {
        return view('cms-static-publishing-livewire::build-monitor', ['builds' => StaticBuild::query()->latest()->limit(20)->get()]);
    }
}
