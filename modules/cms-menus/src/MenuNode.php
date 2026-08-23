<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus;

/**
 * A rendered menu item in a visible menu tree.
 */
final readonly class MenuNode
{
    /**
     * @param  array<int, MenuNode>  $children
     */
    public function __construct(
        public string $label,
        public string $url,
        public array $children = [],
    ) {}
}
