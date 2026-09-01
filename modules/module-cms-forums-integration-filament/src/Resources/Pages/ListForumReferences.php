<?php

declare(strict_types=1);

namespace Liberu\Cms\ForumsIntegrationFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ForumsIntegrationFilament\Resources\ForumReferenceResource;

final class ListForumReferences extends ListRecords
{
    #[\Override]
    protected static string $resource = ForumReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
