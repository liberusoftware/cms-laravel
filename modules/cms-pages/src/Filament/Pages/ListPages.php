<?php

declare(strict_types=1);

namespace Liberu\Cms\Pages\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Pages\Filament\PageResource;

final class ListPages extends ListRecords
{
    #[\Override]
    protected static string $resource = PageResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
