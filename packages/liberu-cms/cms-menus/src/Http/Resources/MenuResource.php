<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Menus\Models\Menu;
use Liberu\Cms\Menus\Models\MenuItem;

/**
 * The Delivery API wire shape for a Menu: its name, location, and the ordered,
 * nested item tree (labels, URLs, children) so a consumer can render multi-level
 * navigation directly.
 *
 * @mixin Menu
 */
final class MenuResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'location' => $this->location,
            'items' => $this->buildTree($this->items()->get()->all(), null),
        ];
    }

    /**
     * @param  array<int, MenuItem>  $items
     * @return array<int, array<string, mixed>>
     */
    private function buildTree(array $items, ?int $parentId): array
    {
        $nodes = [];

        foreach ($items as $item) {
            if ($item->parent_id !== $parentId) {
                continue;
            }

            $nodes[] = [
                'label' => $item->label,
                'url' => $item->url,
                'children' => $this->buildTree($items, $item->id),
            ];
        }

        return $nodes;
    }
}
