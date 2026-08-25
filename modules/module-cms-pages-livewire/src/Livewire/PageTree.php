<?php

declare(strict_types=1);

namespace Liberu\Cms\PagesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Pages\Queries\PageTreeQuery;
use Livewire\Component;

final class PageTree extends Component
{
    public string $search = '';

    private PageTreeQuery $tree;

    public function boot(PageTreeQuery $tree): void
    {
        $this->tree = $tree;
    }

    public function render(): View
    {
        $pages = $this->tree->roots($this->search);

        return view('cms-pages-livewire::livewire.page-tree', ['pages' => $pages]);
    }
}
