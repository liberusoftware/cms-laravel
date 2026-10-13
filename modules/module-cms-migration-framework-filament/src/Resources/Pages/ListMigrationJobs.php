<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFrameworkFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\MigrationFrameworkFilament\Resources\MigrationJobResource;

final class ListMigrationJobs extends ListRecords
{
    #[\Override]
    protected static string $resource = MigrationJobResource::class;
}
