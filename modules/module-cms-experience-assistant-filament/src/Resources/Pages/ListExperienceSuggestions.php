<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ExperienceAssistantFilament\Resources\ExperienceSuggestionResource;

final class ListExperienceSuggestions extends ListRecords
{
    #[\Override]
    protected static string $resource = ExperienceSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
