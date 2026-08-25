<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentCalendar;

use Liberu\Cms\Core\Module\AbstractModule;

final class ContentCalendarModule extends AbstractModule
{
    public function key(): string
    {
        return 'content-calendar';
    }

    public function name(): string
    {
        return 'Content Calendar';
    }

    public function version(): string
    {
        return '0.1.0';
    }
}
