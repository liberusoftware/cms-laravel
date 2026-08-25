<?php

declare(strict_types=1);

namespace Liberu\Cms\ConfigurationManagementFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ConfigurationManagementFilament\Resources\ConfigurationReleaseResource;

final class ListConfigurationReleases extends ListRecords
{
    #[\Override]
    protected static string $resource = ConfigurationReleaseResource::class;
}
