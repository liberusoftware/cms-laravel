<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Taxonomy\Queries\TaxonomyQuery;
use Livewire\Component;

final class TaxonomyBrowser extends Component
{
    public ?int $taxonomyId = null;

    public string $search = '';

    private TaxonomyQuery $taxonomyQuery;

    public function boot(TaxonomyQuery $taxonomyQuery): void
    {
        $this->taxonomyQuery = $taxonomyQuery;
    }

    public function render(): View
    {
        $taxonomy = $this->taxonomyId === null ? null : $this->taxonomyQuery->taxonomy($this->taxonomyId);

        return view('cms-taxonomy-livewire::taxonomy-browser', ['taxonomy' => $taxonomy, 'terms' => $taxonomy ? $this->taxonomyQuery->terms((int) $taxonomy->id, $this->search) : []]);
    }
}
