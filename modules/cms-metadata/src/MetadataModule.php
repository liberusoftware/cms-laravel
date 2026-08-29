<?php

declare(strict_types=1);

namespace Liberu\Cms\Metadata;

use Liberu\Cms\Core\Module\AbstractModule;

final class MetadataModule extends AbstractModule
{
    public function key(): string
    {
        return 'metadata';
    }

    public function name(): string
    {
        return 'Metadata';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
