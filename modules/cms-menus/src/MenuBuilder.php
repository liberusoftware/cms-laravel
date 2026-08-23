<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus;

use Liberu\Cms\Contracts\Access\AccessControlInterface;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;

/**
 * Builds the visible, nested menu tree for the current user.
 *
 * An item that declares a required permission is hidden — along with its whole
 * subtree — from users who lack it, authorising through the access contract so
 * this module never touches roles or the users table directly.
 */
final readonly class MenuBuilder
{
    public function __construct(private AccessControlInterface $access) {}

    /**
     * @return array<int, MenuNode>
     */
    public function tree(Menu $menu): array
    {
        $byParent = [];

        foreach ($menu->items()->get() as $item) {
            $byParent[$item->parent_id ?? 0][] = $item;
        }

        return $this->buildLevel($byParent, 0);
    }

    /**
     * @param  array<int, array<int, MenuItem>>  $byParent
     * @return array<int, MenuNode>
     */
    private function buildLevel(array $byParent, int $parentId): array
    {
        $nodes = [];

        foreach ($byParent[$parentId] ?? [] as $item) {
            if (! $this->isVisible($item)) {
                continue;
            }

            $nodes[] = new MenuNode($item->label, $item->url, $this->buildLevel($byParent, $item->id));
        }

        return $nodes;
    }

    private function isVisible(MenuItem $item): bool
    {
        return $item->permission === null || $this->access->can($item->permission);
    }
}
