<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeIntegrationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ThemeIntegrationFilament\Resources\ThemeBindingResource;

final class ListThemeBindings extends ListRecords
{
    #[\Override]
    protected static string $resource = ThemeBindingResource::class;
}
