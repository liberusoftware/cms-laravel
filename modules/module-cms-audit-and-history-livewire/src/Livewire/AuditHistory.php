<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Audit\Models\AuditLog;
use Livewire\Component;

final class AuditHistory extends Component
{
    public string $action = '';

    public function render(): View
    {
        if (auth()->user()?->can('audit.view') !== true) {
            return view('cms-audit-and-history-livewire::audit-history', ['logs' => collect()]);
        }

        $query = AuditLog::query()->latest('created_at')->limit(25);
        if ($this->action !== '') {
            $query->where('action', $this->action);
        }

        return view('cms-audit-and-history-livewire::audit-history', ['logs' => $query->get()]);
    }
}
