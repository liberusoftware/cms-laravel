<?php

declare(strict_types=1);

namespace Liberu\Cms\Contracts\Block;

/**
 * A kind of content block (text, image, CTA, …). Given its data and the
 * already-rendered HTML of any nested children, it returns its own HTML.
 *
 * @api This interface is part of the public extension API.
 */
interface BlockTypeInterface
{
    public function key(): string;

    /**
     * @param  array<array-key, mixed>  $data
     */
    public function render(array $data, string $childrenHtml = ''): string;
}
