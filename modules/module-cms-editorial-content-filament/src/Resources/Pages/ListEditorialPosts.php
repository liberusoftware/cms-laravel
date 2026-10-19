<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\EditorialContentFilament\Resources\EditorialPostResource;

final class ListEditorialPosts extends ListRecords
{
    #[\Override]
    protected static string $resource = EditorialPostResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
