<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContent;

use Liberu\Cms\Core\Module\AbstractModule;

final class EventsContentModule extends AbstractModule
{
    public function key(): string
    {
        return 'events-content';
    }

    public function name(): string
    {
        return 'Events Content';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
