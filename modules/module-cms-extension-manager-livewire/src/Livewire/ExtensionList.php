<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManagerLivewire\Livewire;

use Liberu\Cms\ExtensionManager\Services\ExtensionManagerService;
use Livewire\Component;

final class ExtensionList extends Component
{
    public function render(ExtensionManagerService $extensions): mixed
    {
        return view('module-cms-extension-manager::extension-list', ['extensions' => $extensions->all()]);
    }
}
