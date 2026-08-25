<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\SeoFilament\Resources\SeoMetadataResource;

final class ListSeoMetadata extends ListRecords
{
    #[\Override]
    protected static string $resource = SeoMetadataResource::class;
}
