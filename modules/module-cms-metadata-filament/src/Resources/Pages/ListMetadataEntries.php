<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\MetadataFilament\Resources\MetadataEntryResource;

final class ListMetadataEntries extends ListRecords
{
    protected static string $resource = MetadataEntryResource::class;
}
