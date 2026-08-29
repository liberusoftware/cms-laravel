<?php

declare(strict_types=1);

namespace Liberu\Cms\BlockEditor;

use Liberu\Cms\Core\Module\AbstractModule;

final class BlockEditorModule extends AbstractModule
{
    public function key(): string
    {
        return 'block-editor';
    }

    public function name(): string
    {
        return 'Block Editor';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
