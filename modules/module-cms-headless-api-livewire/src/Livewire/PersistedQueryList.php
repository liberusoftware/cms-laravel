<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\HeadlessApi\Models\PersistedQuery;
use Livewire\Component;

final class PersistedQueryList extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 64);
    }

    public function render(): View
    {
        $queries = PersistedQuery::query()->when($this->search !== '', fn ($query) => $query->where('query_hash', 'like', $this->search.'%'))->latest()->paginate(15);

        return view('cms-headless-api-livewire::livewire.persisted-query-list', ['queries' => $queries]);
    }
}
