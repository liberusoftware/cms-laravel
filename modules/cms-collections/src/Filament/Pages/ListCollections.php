<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Collections\Actions\CollectionMutationService;
use Liberu\Cms\Collections\Filament\CollectionResource;

final class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(fn (array $data) => app(CollectionMutationService::class)->create($data))];
    }
}
