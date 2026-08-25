<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContentFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\RelatedContentFilament\Resources\RelatedContentResource;

final class ListRelatedContent extends ListRecords
{
    #[\Override]
    protected static string $resource = RelatedContentResource::class;
}
