<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

final class TextBlock extends AbstractBlockType
{
    public function key(): string
    {
        return 'text';
    }

    public function render(array $data, string $childrenHtml = ''): string
    {
        return '<div class="cms-block cms-block-text">'.nl2br($this->e($this->str($data, 'text'))).'</div>';
    }
}
