<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentGovernanceLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentGovernance\Services\ContentGovernanceService;
use Livewire\Component;

final class GovernanceOverview extends Component
{
    public function render(): View
    {
        return view()->make('module-cms-content-governance::governance-overview', ['records' => app(ContentGovernanceService::class)->records(auth()->user()?->current_team_id)]);
    }
}
