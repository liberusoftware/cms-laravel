<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionMarketplaceFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ExtensionMarketplaceFilament\Resources\ExtensionListingResource;

final class ListExtensionListings extends ListRecords
{
    protected static string $resource = ExtensionListingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
