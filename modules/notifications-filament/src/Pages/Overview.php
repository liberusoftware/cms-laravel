<?php

declare(strict_types=1);

namespace Liberu\Foundation\NotificationsFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'notifications-filament::overview';

    #[\Override]
    protected static ?string $title = 'Notifications';
}
