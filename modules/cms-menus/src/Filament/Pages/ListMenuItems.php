<?php

declare(strict_types=1);

namespace Liberu\Cms\Menus\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Menus\Filament\MenuItemResource;

final class ListMenuItems extends ListRecords
{
    #[\Override]
    protected static string $resource = MenuItemResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
