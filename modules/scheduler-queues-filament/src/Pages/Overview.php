<?php

declare(strict_types=1);

namespace Liberu\Foundation\SchedulerQueuesFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'scheduler-queues-filament::overview';

    #[\Override]
    protected static ?string $title = 'Scheduler and Queues';
}
