<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Taxonomy\Models\Taxonomy;
use Liberu\Cms\Taxonomy\Services\TaxonomyService;
use Livewire\Component;

final class TaxonomyBrowser extends Component
{
    public ?int $taxonomyId = null;

    public string $search = '';

    public function render(TaxonomyService $service): View
    {
        $taxonomy = $this->taxonomyId === null ? null : Taxonomy::query()->find($this->taxonomyId);

        return view('cms-taxonomy-livewire::taxonomy-browser', ['taxonomy' => $taxonomy, 'terms' => $taxonomy ? $service->terms($taxonomy, $this->search) : []]);
    }
}
