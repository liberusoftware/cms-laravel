<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

final class HeadingBlock extends AbstractBlockType
{
    public function key(): string
    {
        return 'heading';
    }

    public function render(array $data, string $childrenHtml = ''): string
    {
        $level = max(1, min(6, $this->int($data, 'level', 2)));

        return "<h{$level} class=\"cms-block-heading\">".$this->e($this->str($data, 'text'))."</h{$level}>";
    }
}
