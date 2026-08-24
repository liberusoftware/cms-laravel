<?php

declare(strict_types=1);

namespace Liberu\Cms\Forms\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Forms\Filament\FormResource;

final class ListForms extends ListRecords
{
    #[\Override]
    protected static string $resource = FormResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
