<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentAccess\Services\ContentAccessService;
use Livewire\Component;

final class AccessChecker extends Component
{
    public string $subjectType = 'page';

    public string $subjectKey = '';

    public bool $preview = false;

    public function render(): View
    {
        $allowed = $this->subjectKey !== '' && app(ContentAccessService::class)->canAccess($this->subjectType, $this->subjectKey, auth()->user()?->current_team_id, [], $this->preview);

        return view()->make('module-cms-content-access::access-checker', ['allowed' => $allowed]);
    }
}
