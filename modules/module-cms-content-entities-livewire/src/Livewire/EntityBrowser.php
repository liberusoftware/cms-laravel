<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class EntityBrowser extends Component
{
    use WithPagination;

    public string $type = '';

    public string $search = '';

    public int $perPage = 10;

    private PublishedEntityQuery $entitiesQuery;

    public function mount(string $type): void
    {
        $type = trim($type);

        if ($type === '' || mb_strlen($type) > 255) {
            throw ValidationException::withMessages(['type' => 'A valid content entity type is required.']);
        }

        $this->type = $type;
    }

    public function boot(PublishedEntityQuery $entitiesQuery): void
    {
        $this->entitiesQuery = $entitiesQuery;
    }

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 255);
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, min($this->perPage, 100));
        $this->resetPage();
    }

    public function render(): View
    {
        $entities = $this->entitiesQuery->forType($this->type, $this->perPage, $this->search);

        return view('cms-content-entities-livewire::livewire.entity-browser', ['entities' => $entities]);
    }
}
