<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Cms\Core\Http\Concerns\FiltersApiResource;
use Liberu\Cms\Menus\MenuBuilder;
use Liberu\Cms\Menus\MenuNode;
use Liberu\Cms\Menus\Models\Menu;

/**
 * The Delivery API wire shape for a Menu: its name, location, and the ordered,
 * nested item tree (labels, URLs, children) so a consumer can render multi-level
 * navigation directly.
 *
 * @mixin Menu
 */
final class MenuResource extends JsonResource
{
    use FiltersApiResource;

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return $this->withApiResourceFilter([
            'id' => (string) $this->id,
            'type' => 'cms-navigation',
            'name' => $this->name,
            'location' => $this->location,
            'variant' => $this->variant,
            'items' => collect($this->resource instanceof Menu ? app(MenuBuilder::class)->tree($this->resource, $request->query('path')) : [])
                ->map(fn (MenuNode $node): array => $this->node($node))
                ->all(),
        ]);
    }

    /** @return array<string, mixed> */
    private function node(MenuNode $node): array
    {
        return [
            'label' => $node->label,
            'url' => $node->url,
            'link_type' => $node->linkType,
            'active' => $node->active,
            'children' => collect($node->children)->map(fn (MenuNode $child): array => $this->node($child))->all(),
        ];
    }
}
