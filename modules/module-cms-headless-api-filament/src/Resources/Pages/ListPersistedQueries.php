<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\HeadlessApiFilament\Resources\PersistedQueryResource;

final class ListPersistedQueries extends ListRecords
{
    #[\Override]
    protected static string $resource = PersistedQueryResource::class;
}
