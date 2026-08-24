<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Collections\Filament\CollectionItemResource;

final class ListCollectionItems extends ListRecords
{
    protected static string $resource = CollectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
