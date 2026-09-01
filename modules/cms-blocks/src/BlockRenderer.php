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

    /** @param array<mixed, mixed> $block */
    public function render(array $block): string
    {
        return $this->renderAtDepth($block, 0);
    }

    /** @param array<mixed, mixed> $block */
    private function renderAtDepth(array $block, int $depth): string
    {
        $configuredMaxDepth = config('cms-blocks.max_nesting_depth', 32);
        $maxDepth = is_numeric($configuredMaxDepth) ? max(1, (int) $configuredMaxDepth) : 32;
        if ($depth > $maxDepth) {
            return '';
        }

        $key = is_string($block['type'] ?? null) ? $block['type'] : '';
        $type = $this->registry->get($key);

        if (! $type instanceof BlockTypeInterface) {
            return '';
        }

        $data = is_array($block['data'] ?? null) ? $block['data'] : [];
        $children = is_array($block['children'] ?? null) ? $block['children'] : [];

        $html = $type->render($data, $this->renderChildren($children, $depth + 1));

        return $this->hooks->apply(new BlockRenderFilter($html, $key, $data))->html;
    }

    /** @param array<mixed, mixed> $blocks */
    public function renderMany(array $blocks): string
    {
        return $this->renderChildren($blocks, 0);
    }

    /** @param array<mixed, mixed> $blocks */
    private function renderChildren(array $blocks, int $depth): string
    {
        $html = '';

        foreach ($blocks as $block) {
            if (is_array($block)) {
                $html .= $this->renderAtDepth($block, $depth);
            }
        }

        return $html;
    }
}
