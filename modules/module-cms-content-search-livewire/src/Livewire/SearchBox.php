<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentSearchLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Search\Services\ContentSearchService;
use Livewire\Component;

final class SearchBox extends Component
{
    public string $query = '';

    public function render(): View
    {
        return view('module-cms-content-search::search-box', ['results' => $this->query === '' ? [] : app(ContentSearchService::class)->search($this->query, auth()->user()?->current_team_id, 'livewire')]);
    }
}
