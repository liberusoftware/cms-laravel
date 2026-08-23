<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks;

use Liberu\Cms\Contracts\Block\BlockRendererInterface;
use Liberu\Cms\Contracts\Block\BlockTypeInterface;
use Liberu\Cms\Contracts\Hooks\Filters\BlockRenderFilter;
use Liberu\Cms\Contracts\Hooks\HookBusInterface;

/**
 * Recursively renders a block tree. An unknown block type renders to an empty
 * string rather than throwing, so a removed block type never breaks a page.
 *
 * Each block's HTML passes through the {@see BlockRenderFilter} hook point before
 * it is emitted, so an extension can transform block output without replacing the
 * renderer or the block type.
 */
final readonly class BlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private BlockTypeRegistry $registry,
        private HookBusInterface $hooks,
    ) {}

    public function render(array $block): string
    {
        $key = is_string($block['type'] ?? null) ? $block['type'] : '';
        $type = $this->registry->get($key);

        if (! $type instanceof BlockTypeInterface) {
            return '';
        }

        $data = is_array($block['data'] ?? null) ? $block['data'] : [];
        $children = is_array($block['children'] ?? null) ? $block['children'] : [];

        $html = $type->render($data, $this->renderMany($children));

        return $this->hooks->apply(new BlockRenderFilter($html, $key, $data))->html;
    }

    public function renderMany(array $blocks): string
    {
        $html = '';

        foreach ($blocks as $block) {
            if (is_array($block)) {
                $html .= $this->render($block);
            }
        }

        return $html;
    }
}
