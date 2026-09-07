<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentFederationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentFederation\Services\ContentFederationService;
use Livewire\Component;

final class SourceHealth extends Component
{
    public function render(): View
    {
        return view()->make('module-cms-content-federation::source-health', ['sources' => app(ContentFederationService::class)->sources(auth()->user()?->current_team_id)]);
    }
}
