<?php

declare(strict_types=1);

namespace Liberu\Cms\OfflineAndPwaFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\OfflineAndPwaFilament\Resources\PwaConfigurationResource;

final class ListPwaConfigurations extends ListRecords
{
    #[\Override]
    protected static string $resource = PwaConfigurationResource::class;
}
