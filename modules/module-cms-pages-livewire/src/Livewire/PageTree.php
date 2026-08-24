<?php

declare(strict_types=1);

namespace Liberu\Cms\PagesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Pages\Models\Page;
use Livewire\Component;

final class PageTree extends Component
{
    public string $search = '';

    public function render(): View
    {
        $term = trim($this->search);
        $pages = Page::query()->whereNull('parent_id')->with('children.children')
            ->when($term !== '', fn ($query) => $query->where('title', 'like', "%{$term}%"))
            ->orderBy('title')->get();

        return view('cms-pages-livewire::livewire.page-tree', ['pages' => $pages]);
    }
}
