<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendarLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentCalendar\Services\ContentCalendarService;
use Livewire\Component;

final class CalendarBoard extends Component
{
    public ?string $channel = null;

    public ?string $site = null;

    public function render(): View
    {
        return view()->make('module-cms-content-calendar::calendar-board', ['items' => app(ContentCalendarService::class)->items(auth()->user()?->current_team_id, $this->channel, $this->site)]);
    }
}
