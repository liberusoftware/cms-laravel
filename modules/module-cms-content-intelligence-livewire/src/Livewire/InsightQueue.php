<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntelligenceLivewire\Livewire;

use Liberu\Cms\ContentIntelligence\Services\ContentIntelligenceService;
use Livewire\Component;

final class InsightQueue extends Component
{
    public ?string $metric = null;

    public function render(): mixed
    {
        return view()->make('module-cms-content-intelligence::insight-queue', ['insights' => app(ContentIntelligenceService::class)->insights(auth()->user()?->current_team_id, $this->metric, 'open')]);
    }
}
