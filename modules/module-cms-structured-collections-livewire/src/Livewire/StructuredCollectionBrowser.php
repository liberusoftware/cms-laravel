<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\StructuredCollections\Queries\StructuredCollectionQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class StructuredCollectionBrowser extends Component
{
    use WithPagination;

    public string $collection = '';

    public string $search = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(StructuredCollectionQuery $query): View
    {
        $collection = trim($this->collection);
        $records = $collection === '' ? null : $query->records($collection, max(1, min(50, $this->perPage)), $this->search);

        return view('cms-structured-collections-livewire::collection-browser', ['collectionRecord' => $collection === '' ? null : $query->collection($collection), 'records' => $records]);
    }
}
