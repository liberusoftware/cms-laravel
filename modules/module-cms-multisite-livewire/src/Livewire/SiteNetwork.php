<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Core\Models\Site;
use Liberu\Cms\Multisite\MultisiteService;
use Livewire\Component;

final class SiteNetwork extends Component
{
    public function changeStatus(int $siteId, string $status, MultisiteService $service): void
    {
        $service->transition(Site::query()->findOrFail($siteId), $status);
    }

    public function render(): View
    {
        return view('cms-multisite-livewire::site-network', ['sites' => Site::query()->orderBy('key')->get(['id', 'key', 'name', 'domain', 'status'])]);
    }
}
