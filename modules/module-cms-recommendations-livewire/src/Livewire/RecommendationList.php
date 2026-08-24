<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Recommendations\Services\RecommendationService;
use Livewire\Component;

final class RecommendationList extends Component
{
    public string $listKey = '';

    public int $limit = 5;

    public function render(RecommendationService $service): View
    {
        return view('cms-recommendations-livewire::recommendation-list', ['items' => $this->listKey === '' ? [] : $service->recommend($this->listKey, [], null, $this->limit)]);
    }
}
