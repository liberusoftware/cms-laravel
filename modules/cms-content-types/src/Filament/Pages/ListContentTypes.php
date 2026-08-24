<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ContentTypes\Filament\ContentTypeResource;

final class ListContentTypes extends ListRecords
{
    #[\Override]
    protected static string $resource = ContentTypeResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
