<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

final class ImageBlock extends AbstractBlockType
{
    public function key(): string
    {
        return 'image';
    }

    public function render(array $data, string $childrenHtml = ''): string
    {
        return '<img class="cms-block-image" src="'.$this->e($this->str($data, 'src')).'" alt="'.$this->e($this->str($data, 'alt')).'">';
    }
}
