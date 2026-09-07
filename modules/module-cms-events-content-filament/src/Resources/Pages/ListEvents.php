<?php

declare(strict_types=1);

namespace Liberu\Cms\EventsContentFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\EventsContentFilament\Resources\EventResource;

final class ListEvents extends ListRecords
{
    #[\Override]
    protected static string $resource = EventResource::class;
}
