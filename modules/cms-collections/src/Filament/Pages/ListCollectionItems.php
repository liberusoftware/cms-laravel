<?php

declare(strict_types=1);

namespace Liberu\Cms\Collections\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Collections\Actions\CollectionMutationService;
use Liberu\Cms\Collections\Filament\CollectionItemResource;
use Liberu\Cms\Collections\Models\Collection;

final class ListCollectionItems extends ListRecords
{
    protected static string $resource = CollectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->using(function (array $data) {
            $collection = Collection::query()->findOrFail($data['collection_id']);
            unset($data['collection_id']);

            return app(CollectionMutationService::class)->createItem($collection, $data);
        })];
    }
}
