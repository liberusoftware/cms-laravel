<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\OfflineAndPwa\Models\PwaConfiguration;
use Liberu\Cms\OfflineAndPwa\Services\OfflineAndPwaService;
use Livewire\Component;

final class PwaStatus extends Component
{
    public string $siteKey = '';

    public function render(OfflineAndPwaService $service): View
    {
        $configuration = $this->siteKey === '' ? null : PwaConfiguration::query()->where('site_key', $this->siteKey)->first();

        return view('cms-offline-and-pwa-livewire::pwa-status', ['configuration' => $configuration, 'manifest' => $configuration ? $service->manifest($configuration) : null]);
    }
}
