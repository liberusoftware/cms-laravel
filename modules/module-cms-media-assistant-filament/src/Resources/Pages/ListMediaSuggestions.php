<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\MediaAssistantFilament\Resources\MediaSuggestionResource;

final class ListMediaSuggestions extends ListRecords
{
    #[\Override]
    protected static string $resource = MediaSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
