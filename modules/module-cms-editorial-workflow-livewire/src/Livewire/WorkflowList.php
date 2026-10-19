<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\EditorialWorkflow\Queries\EditorialWorkflowQuery;
use Livewire\Component;

final class WorkflowList extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 255);
    }

    public function render(EditorialWorkflowQuery $query): View
    {
        return view('cms-editorial-workflow-livewire::livewire.workflow-list', ['workflows' => $query->paginate(15, $this->search)]);
    }
}
