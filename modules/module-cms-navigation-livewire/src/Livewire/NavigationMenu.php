<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Menus\Contracts\MenuRepositoryInterface;
use Liberu\Cms\Menus\MenuBuilder;
use Liberu\Cms\Menus\Models\Menu;
use Livewire\Component;

final class NavigationMenu extends Component
{
    public string $location = 'header';

    public string $variant = 'default';

    public string $path = '';

    public function render(MenuRepositoryInterface $menus, MenuBuilder $builder): View
    {
        $menu = $menus->forLocation($this->location, $this->variant);

        return view('cms-navigation-livewire::navigation-menu', [
            'nodes' => $menu instanceof Menu ? $builder->tree($menu, $this->path === '' ? null : $this->path) : [],
        ]);
    }
}
