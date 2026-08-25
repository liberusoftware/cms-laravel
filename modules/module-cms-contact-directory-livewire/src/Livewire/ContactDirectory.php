<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryLivewire\Livewire;

use Liberu\Cms\ContactDirectory\Services\ContactDirectoryService;
use Livewire\Component;

final class ContactDirectory extends Component
{
    public int $pageSize = 25;

    public function render(): mixed
    {
        return view('module-cms-contact-directory::contact-directory', ['contacts' => app(ContactDirectoryService::class)->contacts(auth()->user()?->current_team_id, true, $this->pageSize)]);
    }
}
