<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Experimentation\Queries\ExperimentationQuery;
use Livewire\Component;

final class ExperimentList extends Component
{
    public string $search = '';

    public function render(ExperimentationQuery $query): View
    {
        return view('cms-experimentation-livewire::experiment-list', ['experiments' => $query->list(24, $this->search)]);
    }
}
