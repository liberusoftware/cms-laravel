<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Publishing\Models\PublicationRelease;
use Liberu\Cms\Publishing\Services\PublishingService;
use Livewire\Component;

final class ReleaseMonitor extends Component
{
    public string $releaseKey = '';

    public function publish(PublishingService $service): void
    {
        $service->publish(PublicationRelease::query()->where('key', $this->releaseKey)->firstOrFail());
    }

    public function unpublish(PublishingService $service): void
    {
        $service->unpublish(PublicationRelease::query()->where('key', $this->releaseKey)->firstOrFail());
    }

    public function render(): View
    {
        return view('cms-publishing-livewire::release-monitor', ['release' => PublicationRelease::query()->where('key', $this->releaseKey)->first()]);
    }
}
