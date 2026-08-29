<?php

declare(strict_types=1);

namespace Liberu\Cms\JoomlaMigrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\JoomlaMigrationFilament\Resources\JoomlaMigrationResource;

final class ListJoomlaMigrations extends ListRecords
{
    protected static string $resource = JoomlaMigrationResource::class;
}
