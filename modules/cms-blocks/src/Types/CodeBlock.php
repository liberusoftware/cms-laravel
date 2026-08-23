<?php

declare(strict_types=1);

namespace Liberu\Cms\Blocks\Types;

final class CodeBlock extends AbstractBlockType
{
    public function key(): string
    {
        return 'code';
    }

    public function render(array $data, string $childrenHtml = ''): string
    {
        $language = $this->e($this->str($data, 'language', 'plaintext'));

        return '<pre class="cms-block-code"><code class="language-'.$language.'">'.$this->e($this->str($data, 'code')).'</code></pre>';
    }
}
