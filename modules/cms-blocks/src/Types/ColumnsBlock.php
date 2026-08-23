<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

/**
 * A layout container that renders its nested child blocks inside a grid — the
 * basis of the page builder's nesting.
 */
final class ColumnsBlock extends AbstractBlockType
{
    public function key(): string
    {
        return 'columns';
    }

    public function render(array $data, string $childrenHtml = ''): string
    {
        $count = max(1, min(6, $this->int($data, 'columns', 2)));

        return '<div class="cms-block-columns cms-columns-'.$count.'">'.$childrenHtml.'</div>';
    }
}
