<?php

declare(strict_types=1);

namespace Liberu\Cms\WordPressMigrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\WordPressMigrationFilament\Resources\WordPressMigrationResource;

final class ListWordPressMigrations extends ListRecords
{
    protected static string $resource = WordPressMigrationResource::class;
}
