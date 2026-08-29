<?php

declare(strict_types=1);

namespace Liberu\Cms\NavigationFilament\Resources\NavigationResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\NavigationFilament\Resources\NavigationResource;

final class ListNavigation extends ListRecords
{
    #[\Override]
    protected static string $resource = NavigationResource::class;
}
