<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ThemeMarketplace\Models\MarketplaceTheme;
use Livewire\Component;

final class ThemeCatalog extends Component
{
    public string $search = '';

    public function render(): View
    {
        return view('cms-theme-marketplace-livewire::theme-catalog', ['themes' => MarketplaceTheme::query()->where('status', 'published')->when($this->search !== '', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))->latest()->get()]);
    }
}
