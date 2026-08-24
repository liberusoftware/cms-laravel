<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Core\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;

final class SiteList extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);
        $sites = Site::query()
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('key', 'like', "%{$search}%")))
            ->latest('id')
            ->paginate(max(1, min(50, $this->perPage)));

        return view('cms-core-livewire::livewire.site-list', compact('sites'));
    }
}
