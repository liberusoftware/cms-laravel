<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryLivewire\Livewire;

use Liberu\Cms\IntegrationDirectory\Models\Integration;
use Livewire\Component;

final class IntegrationList extends Component
{
    public function render(): mixed
    {
        return view('module-cms-integration-directory::integration-list', ['integrations' => Integration::query()->orderBy('name')->limit(50)->get()]);
    }
}
