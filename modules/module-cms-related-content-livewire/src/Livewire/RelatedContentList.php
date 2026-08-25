<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContentLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\RelatedContent\Services\RelatedContentService;
use Livewire\Component;

final class RelatedContentList extends Component
{
    public string $sourceType = '';

    public int $sourceId = 0;

    public function render(RelatedContentService $service): View
    {
        return view('cms-related-content-livewire::related-content-list', ['items' => $this->sourceType === '' || $this->sourceId === 0 ? [] : $service->related($this->sourceType, $this->sourceId)]);
    }
}
