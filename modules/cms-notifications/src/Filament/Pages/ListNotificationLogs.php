<?php

declare(strict_types=1);

namespace Liberu\Cms\Notifications\Filament\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Notifications\Filament\NotificationLogResource;

final class ListNotificationLogs extends ListRecords
{
    #[\Override]
    protected static string $resource = NotificationLogResource::class;
}
