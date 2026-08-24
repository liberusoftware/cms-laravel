<?php

declare(strict_types=1);

namespace Liberu\Cms\RichTextEditor;

use Liberu\Cms\Core\Module\AbstractModule;

final class RichTextEditorModule extends AbstractModule
{
    public function key(): string
    {
        return 'rich-text-editor';
    }

    public function name(): string
    {
        return 'Rich Text Editor';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
