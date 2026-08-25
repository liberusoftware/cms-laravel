<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ConfigurationManagement\Services\ConfigurationService;
use Livewire\Component;

final class ConfigurationReleases extends Component
{
    public string $environment = 'production';

    public function render(ConfigurationService $service): View
    {
        return view('module-cms-configuration-management-livewire::configuration-releases', ['releases' => $service->releases(auth()->user()?->current_team_id, $this->environment)]);
    }
}
