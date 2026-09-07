<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\FieldSystemFilament\Resources\FieldSchemaResource;

final class ListFieldSchemas extends ListRecords
{
    #[\Override]
    protected static string $resource = FieldSchemaResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
