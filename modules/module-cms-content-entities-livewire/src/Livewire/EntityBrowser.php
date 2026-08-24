<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Livewire\Component;
use Livewire\WithPagination;

final class EntityBrowser extends Component
{
    use WithPagination;

    public string $type = '';

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $term = trim($this->search);
        $entities = ContentEntry::query()
            ->whereRelation('type', 'key', $this->type)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($term !== '', fn ($query) => $query->where('title', 'like', "%{$term}%"))
            ->latest('published_at')
            ->paginate(10);

        return view('cms-content-entities-livewire::livewire.entity-browser', compact('entities'));
    }
}
