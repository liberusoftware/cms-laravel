<?php

namespace Liberu\Cms\EmbedsLivewire\Livewire;

use Liberu\Cms\Embeds\Queries\EmbedsQuery;
use Livewire\Component;

class EmbedCatalog extends Component
{
    public string $search = '';

    public function render()
    {
        return view('cms-embeds-livewire::embed-catalog', ['embeds' => app(EmbedsQuery::class)->list(24, $this->search)]);
    }
}
