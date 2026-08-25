<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Core\Queries\CoreQueryService;
use Livewire\Component;
use Livewire\WithPagination;

final class ChannelList extends Component
{
    use WithPagination;

    public string $site = '';

    public string $search = '';

    public int $perPage = 10;

    private CoreQueryService $queries;

    public function mount(string $site): void
    {
        $this->site = trim($site);
    }

    public function boot(CoreQueryService $queries): void
    {
        $this->queries = $queries;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, min($this->perPage, 100));
        $this->resetPage();
    }

    public function render(): View
    {
        $channels = $this->queries->channels($this->site, $this->perPage, trim($this->search));

        return view('cms-core-livewire::livewire.channel-list', compact('channels'));
    }
}
