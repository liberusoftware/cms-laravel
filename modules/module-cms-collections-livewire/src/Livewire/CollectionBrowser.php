<?php

declare(strict_types=1);

namespace Liberu\Cms\CollectionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Collections\Models\Collection;
use Liberu\Cms\Collections\Models\CollectionItem;
use Livewire\Component;
use Livewire\WithPagination;

final class CollectionBrowser extends Component
{
    use WithPagination;

    public string $collection = '';

    public string $search = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $term = trim($this->search);
        $records = CollectionItem::query()
            ->whereHas('collection', fn ($query) => $query->where('slug', $this->collection))
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->when($term !== '', fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%")))
            ->latest('published_at')
            ->paginate(max(1, min(50, $this->perPage)));

        return view('cms-collections-livewire::livewire.collection-browser', [
            'collectionRecord' => Collection::query()->where('slug', $this->collection)->first(),
            'records' => $records,
        ]);
    }
}
