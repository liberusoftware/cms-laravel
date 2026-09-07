<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\EditorialContent\Queries\EditorialContentQuery;
use Livewire\Component;

final class EditorialPostList extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 255);
    }

    public function render(EditorialContentQuery $query): View
    {
        return view('cms-editorial-content-livewire::livewire.editorial-post-list', ['posts' => $query->paginate(15, $this->search)]);
    }
}
