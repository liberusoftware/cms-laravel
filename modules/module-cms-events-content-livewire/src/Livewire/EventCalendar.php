<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\EventsContent\Queries\EventsContentQuery;
use Livewire\Component;

final class EventCalendar extends Component
{
    public string $search = '';

    public function render(EventsContentQuery $query): View
    {
        return view('cms-events-content-livewire::event-calendar', ['events' => $query->calendar(24, $this->search)]);
    }
}
