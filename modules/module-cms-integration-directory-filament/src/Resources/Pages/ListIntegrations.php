<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\IntegrationDirectoryFilament\Resources\IntegrationResource;

final class ListIntegrations extends ListRecords
{
    protected static string $resource = IntegrationResource::class;
}
