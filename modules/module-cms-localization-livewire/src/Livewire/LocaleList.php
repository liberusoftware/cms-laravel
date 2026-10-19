<?php

declare(strict_types=1);

namespace Liberu\Cms\LocalizationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Localization\Queries\LocalizationQuery;
use Livewire\Component;

final class LocaleList extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 35);
    }

    public function render(LocalizationQuery $query): View
    {
        return view('cms-localization-livewire::livewire.locale-list', ['locales' => $query->locales(15, auth()->user()?->current_team_id, $this->search)]);
    }
}
