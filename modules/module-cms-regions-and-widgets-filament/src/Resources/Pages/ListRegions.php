<?php

declare(strict_types=1);

namespace Liberu\Cms\RegionsAndWidgetsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\RegionsAndWidgetsFilament\Resources\RegionResource;

final class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;
}
