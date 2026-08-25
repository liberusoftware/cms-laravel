<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentAccessLivewire\Livewire;

use Liberu\Cms\ContentAccess\Services\ContentAccessService;
use Livewire\Component;

final class AccessChecker extends Component
{
    public string $subjectType = 'page';

    public string $subjectKey = '';

    public bool $preview = false;

    public function render(): mixed
    {
        $allowed = $this->subjectKey !== '' && app(ContentAccessService::class)->canAccess($this->subjectType, $this->subjectKey, auth()->user()?->current_team_id, [], $this->preview);

        return view('module-cms-content-access::access-checker', ['allowed' => $allowed]);
    }
}
