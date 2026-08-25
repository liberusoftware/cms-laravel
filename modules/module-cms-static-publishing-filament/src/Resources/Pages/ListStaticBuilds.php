<?php

declare(strict_types=1);

namespace Liberu\Cms\StaticPublishingFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\StaticPublishingFilament\Resources\StaticBuildResource;

final class ListStaticBuilds extends ListRecords
{
    #[\Override]
    protected static string $resource = StaticBuildResource::class;
}
