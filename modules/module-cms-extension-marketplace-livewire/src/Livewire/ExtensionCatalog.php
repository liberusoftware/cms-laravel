<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ExtensionMarketplace\Queries\ExtensionMarketplaceQuery;
use Livewire\Component;

final class ExtensionCatalog extends Component
{
    public string $search = '';

    public function render(ExtensionMarketplaceQuery $query): View
    {
        return view('cms-extension-marketplace-livewire::extension-catalog', ['listings' => $query->catalog(24, $this->search)]);
    }
}
