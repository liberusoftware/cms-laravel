<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Repositories;

use Liberu\Cms\Menus\Contracts\MenuRepositoryInterface;
use Liberu\Cms\Menus\Models\Menu;

final class MenuRepository implements MenuRepositoryInterface
{
    public function find(int $id): ?Menu
    {
        return Menu::query()->find($id);
    }

    public function forLocation(string $location, string $variant = 'default'): ?Menu
    {
        return Menu::query()->where('location', $location)->where('variant', $variant)->first();
    }
}
