<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentLocking\Models\ContentLock;
use Livewire\Component;

final class LockPresence extends Component
{
    public string $subjectType = 'page';

    public string $subjectKey = '';

    public function render(): View
    {
        return view('module-cms-content-locking::lock-presence', ['lock' => ContentLock::query()->where(['team_id' => auth()->user()?->current_team_id, 'subject_type' => $this->subjectType, 'subject_key' => $this->subjectKey])->first()]);
    }
}
