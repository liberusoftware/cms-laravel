<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ContentEntitiesFilament\Resources\ContentEntityResource;

final class ListContentEntities extends ListRecords
{
    #[\Override]
    protected static string $resource = ContentEntityResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
