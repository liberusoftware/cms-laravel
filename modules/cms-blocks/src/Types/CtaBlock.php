<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

final class CtaBlock extends AbstractBlockType
{
    public function key(): string
    {
        return 'cta';
    }

    public function render(array $data, string $childrenHtml = ''): string
    {
        return '<a class="cms-block-cta" href="'.$this->e($this->str($data, 'url', '#')).'">'.$this->e($this->str($data, 'label')).'</a>';
    }
}
