<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ThemeMarketplace\Queries\ThemeMarketplaceQuery;
use Livewire\Component;

final class ThemeCatalog extends Component
{
    public string $search = '';

    public function render(ThemeMarketplaceQuery $themes): View
    {
        return view('cms-theme-marketplace-livewire::theme-catalog', ['themes' => $themes->catalog(24, $this->search)]);
    }
}
