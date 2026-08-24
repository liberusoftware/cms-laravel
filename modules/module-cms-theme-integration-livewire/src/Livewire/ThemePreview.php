<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ThemeIntegration\Services\ThemeIntegrationService;
use Livewire\Component;

final class ThemePreview extends Component
{
    public string $siteKey = '';

    public string $channelKey = '';

    public function render(ThemeIntegrationService $service): View
    {
        $theme = $this->siteKey === '' ? 'default' : $service->effectiveTheme($this->siteKey, $this->channelKey !== '' ? $this->channelKey : null);

        return view('cms-theme-integration-livewire::theme-preview', ['theme' => $theme, 'components' => $service->components($theme)]);
    }
}
