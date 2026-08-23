<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Block;

/**
 * Renders a JSON-shaped block tree to HTML. A block is
 * `['type' => string, 'data' => array, 'children' => array]`; children are
 * rendered recursively, enabling nested blocks and page-builder layouts.
 *
 * @api This interface is part of the public extension API.
 */
interface BlockRendererInterface
{
    /**
     * @param  array<array-key, mixed>  $block
     */
    public function render(array $block): string;

    /**
     * @param  array<array-key, mixed>  $blocks
     */
    public function renderMany(array $blocks): string;
}
