<?php

declare(strict_types=1);

namespace Liberu\Cms\DigitalAssetManagementLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\DigitalAssetManagement\Services\DigitalAssetManagementService;
use Livewire\Component;

final class AssetLibrary extends Component
{
    public ?string $status = null;

    public function render(): View
    {
        return view('module-cms-digital-asset-management::asset-library', ['assets' => app(DigitalAssetManagementService::class)->assets(auth()->user()?->current_team_id, $this->status)]);
    }
}
