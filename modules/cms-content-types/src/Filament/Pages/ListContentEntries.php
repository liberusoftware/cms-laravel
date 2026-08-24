<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ContentTypes\Filament\ContentEntryResource;

final class ListContentEntries extends ListRecords
{
    #[\Override]
    protected static string $resource = ContentEntryResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
