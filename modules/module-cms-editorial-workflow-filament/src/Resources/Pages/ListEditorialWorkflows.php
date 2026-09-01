<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialWorkflowFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\EditorialWorkflowFilament\Resources\EditorialWorkflowResource;

final class ListEditorialWorkflows extends ListRecords
{
    #[\Override]
    protected static string $resource = EditorialWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
