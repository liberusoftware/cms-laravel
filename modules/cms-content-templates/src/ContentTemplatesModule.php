<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplates;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentTemplatesModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-templates';
    }

    public function name(): string
    {
        return 'Content Templates';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
