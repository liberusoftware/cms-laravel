<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentTypesModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-types';
    }

    public function name(): string
    {
        return 'Custom Content Types';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
