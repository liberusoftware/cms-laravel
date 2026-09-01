<?php

namespace Liberu\Cms\EmbedsLivewire\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Liberu\Cms\Embeds\Queries\EmbedsQuery;
use Livewire\Component;

class EmbedCatalog extends Component
{
    public string $search = '';

    public function render(): Factory|View
    {
        return view('cms-embeds-livewire::embed-catalog', ['embeds' => app(EmbedsQuery::class)->list(24, $this->search)]);
    }
}
