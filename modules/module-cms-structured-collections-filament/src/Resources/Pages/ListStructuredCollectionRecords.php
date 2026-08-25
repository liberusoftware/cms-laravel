<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\StructuredCollectionsFilament\Resources\StructuredCollectionRecordResource;

final class ListStructuredCollectionRecords extends ListRecords
{
    #[\Override]
    protected static string $resource = StructuredCollectionRecordResource::class;
}
