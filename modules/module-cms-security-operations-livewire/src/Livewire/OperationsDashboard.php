<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\SecurityOperations\Models\SecurityOperation;
use Livewire\Component;

final class OperationsDashboard extends Component
{
    public function render(): View
    {
        return view('cms-security-operations-livewire::operations-dashboard', ['operations' => SecurityOperation::query()->latest()->limit(20)->get()]);
    }
}
