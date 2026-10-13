<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\DrupalMigrationFilament\Resources\DrupalMigrationResource;

final class ListDrupalMigrations extends ListRecords
{
    #[\Override]
    protected static string $resource = DrupalMigrationResource::class;
}
