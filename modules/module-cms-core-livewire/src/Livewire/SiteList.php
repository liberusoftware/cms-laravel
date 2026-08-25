<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Core\Queries\CoreQueryService;
use Livewire\Component;
use Livewire\WithPagination;

final class SiteList extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public string $search = '';

    private CoreQueryService $queries;

    public function boot(CoreQueryService $queries): void
    {
        $this->queries = $queries;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);
        $sites = $this->queries->sites($this->perPage, $search);

        return view('cms-core-livewire::livewire.site-list', ['sites' => $sites]);
    }
}
