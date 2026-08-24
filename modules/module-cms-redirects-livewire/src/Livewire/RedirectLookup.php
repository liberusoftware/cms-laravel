<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Redirects\Services\RedirectService;
use Livewire\Component;

final class RedirectLookup extends Component
{
    public string $path = '';

    public function render(RedirectService $service): View
    {
        return view('cms-redirects-livewire::redirect-lookup', ['result' => $this->path === '' ? null : $service->resolve($this->path)]);
    }
}
