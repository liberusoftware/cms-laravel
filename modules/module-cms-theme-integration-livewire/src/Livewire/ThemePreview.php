<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ThemeIntegration\Services\ThemeIntegrationService;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

final class ThemePreview extends Component
{
    public string $siteKey = '';

    public string $channelKey = '';

    public function updatedSiteKey(): void
    {
        if (strlen($this->siteKey) > 100) {
            throw ValidationException::withMessages(['siteKey' => 'Site keys must be 100 characters or fewer.']);
        }
    }

    public function updatedChannelKey(): void
    {
        if (strlen($this->channelKey) > 100) {
            throw ValidationException::withMessages(['channelKey' => 'Channel keys must be 100 characters or fewer.']);
        }
    }

    public function render(ThemeIntegrationService $service): View
    {
        $theme = $this->siteKey === '' ? 'default' : $service->effectiveTheme($this->siteKey, $this->channelKey !== '' ? $this->channelKey : null);

        return view('cms-theme-integration-livewire::theme-preview', ['theme' => $theme, 'components' => $service->components($theme)]);
    }
}
