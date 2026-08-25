<?php

declare(strict_types=1);

namespace Liberu\Cms\CollectionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Collections\Queries\CollectionQuery;
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

    public function render(CollectionQuery $collections): View
    {
        $records = $collections->published($this->collection, max(1, min(50, $this->perPage)), $this->search);

        return view('cms-collections-livewire::livewire.collection-browser', [
            'collectionRecord' => $collections->publishedCollection($this->collection),
            'records' => $records,
        ]);
    }
}
