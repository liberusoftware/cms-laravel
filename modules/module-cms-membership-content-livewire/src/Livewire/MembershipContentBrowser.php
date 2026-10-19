<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\MembershipContent\Models\MembershipContent;
use Liberu\Cms\MembershipContent\Services\MembershipContentService;
use Livewire\Component;

final class MembershipContentBrowser extends Component
{
    public string $subjectType = 'user';

    public string $subjectKey = '';

    public function render(MembershipContentService $service): View
    {
        $teamId = auth()->user()?->current_team_id;
        $items = collect($service->list($teamId, 50)->items())
            ->filter(fn (mixed $content): bool => $content instanceof MembershipContent
                && $this->subjectKey !== ''
                && $service->canAccess($content, $this->subjectType, $this->subjectKey, $teamId))
            ->values();

        return view('module-cms-membership-content-livewire::browser', ['items' => $items]);
    }
}
