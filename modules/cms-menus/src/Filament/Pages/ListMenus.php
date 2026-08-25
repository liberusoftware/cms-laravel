<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Menus\Filament\MenuResource;

final class ListMenus extends ListRecords
{
    #[\Override]
    protected static string $resource = MenuResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
