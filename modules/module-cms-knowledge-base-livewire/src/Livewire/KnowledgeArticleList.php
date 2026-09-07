<?php

declare(strict_types=1);

namespace Liberu\Cms\KnowledgeBaseLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\KnowledgeBase\Queries\KnowledgeBaseQuery;
use Livewire\Component;

final class KnowledgeArticleList extends Component
{
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->search = mb_substr(trim($this->search), 0, 255);
    }

    public function render(KnowledgeBaseQuery $query): View
    {
        return view('cms-knowledge-base-livewire::livewire.knowledge-article-list', ['articles' => $query->paginate(15, $this->search)]);
    }
}
