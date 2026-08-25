<?php

declare(strict_types=1);

namespace Liberu\Cms\ThemeMarketplaceFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ThemeMarketplace\Services\ThemeMarketplaceService;
use Liberu\Cms\ThemeMarketplaceFilament\Resources\MarketplaceThemeResource;

final class ListMarketplaceThemes extends ListRecords
{
    #[\Override]
    protected static string $resource = MarketplaceThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(fn (array $data) => app(ThemeMarketplaceService::class)->publish($data))];
    }
}
