<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\SiteFactory\Services\SiteFactoryService;
use Livewire\Component;

final class SiteProvisioner extends Component
{
    public string $key = '';

    public string $name = '';

    public ?string $message = null;

    public function provision(SiteFactoryService $service): void
    {
        $this->validate(['key' => ['required', 'alpha_dash'], 'name' => ['required', 'string']]);
        $service->provision($this->key, $this->name);
        $this->message = 'Site provisioned.';
    }

    public function render(): View
    {
        return view('cms-site-factory-livewire::site-provisioner');
    }
}
