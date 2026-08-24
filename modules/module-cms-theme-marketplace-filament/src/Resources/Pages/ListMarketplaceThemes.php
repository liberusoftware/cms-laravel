<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ThemeMarketplaceFilament\Resources\MarketplaceThemeResource;

final class ListMarketplaceThemes extends ListRecords
{
    protected static string $resource = MarketplaceThemeResource::class;
}
