<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Hooks\Filters;

use Liberu\Cms\Contracts\Hooks\Filter;

/**
 * Presentation-layer filter point: transforms a single block's rendered HTML
 * before it is emitted. A callback may rewrite, wrap, or annotate {@see $html}
 * using the read-only block {@see $blockType} and {@see $data} as context.
 *
 * @api This filter point is part of the public extension API.
 */
final class BlockRenderFilter implements Filter
{
    /**
     * @param  string  $html  The rendered HTML; mutate this to transform output.
     * @param  string  $blockType  The block's type key, e.g. "text" (read-only context).
     * @param  array<array-key, mixed>  $data  The block's data (read-only context).
     */
    public function __construct(
        public string $html,
        public readonly string $blockType,
        public readonly array $data,
    ) {}

    public function name(): string
    {
        return 'blocks.render';
    }
}
