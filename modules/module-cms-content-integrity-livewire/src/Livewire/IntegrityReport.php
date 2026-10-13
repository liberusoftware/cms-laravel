<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityLivewire\Livewire;

use Liberu\Cms\ContentIntegrity\Services\ContentIntegrityService;
use Livewire\Component;

final class IntegrityReport extends Component
{
    public ?string $status = 'open';

    public function render(): mixed
    {
        return view()->make('module-cms-content-integrity::integrity-report', ['findings' => app(ContentIntegrityService::class)->findings(auth()->user()?->current_team_id, $this->status)]);
    }
}
