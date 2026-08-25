<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\StructuredCollectionsFilament\Resources\StructuredCollectionResource;

final class ListStructuredCollections extends ListRecords
{
    #[\Override]
    protected static string $resource = StructuredCollectionResource::class;
}
