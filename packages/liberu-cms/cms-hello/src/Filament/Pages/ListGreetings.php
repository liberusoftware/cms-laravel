<?php

declare(strict_types=1);

namespace Liberu\Cms\Hello\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Hello\Filament\GreetingResource;

final class ListGreetings extends ListRecords
{
    protected static string $resource = GreetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
