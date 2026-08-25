<?php

declare(strict_types=1);

namespace Liberu\Cms\ViewsAndQueryBuilderLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ListingQueryService;
use Liberu\Cms\ViewsAndQueryBuilder\Queries\ViewDefinitionQuery;
use Livewire\Component;
use Livewire\WithPagination;

final class ViewBrowser extends Component
{
    use WithPagination;

    public string $view = '';

    public string $search = '';

    public int $perPage = 15;

    public function mount(string $view): void
    {
        $this->view = trim($view);
        if ($this->view === '' || strlen($this->view) > 255) {
            throw ValidationException::withMessages(['view' => 'A valid view slug is required.']);
        }
    }

    public function updatedSearch(): void
    {
        $this->search = substr(trim($this->search), 0, 255);
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, min(100, $this->perPage));
        $this->resetPage();
    }

    public function render(ViewDefinitionQuery $views, ListingQueryService $listings): View
    {
        $definition = $views->findPublished($this->view);
        $records = $definition ? $listings->execute($definition, $this->perPage, []) : null;

        return view('module-cms-views-and-query-builder-livewire::livewire.view-browser', ['definition' => $definition, 'records' => $records]);
    }
}
