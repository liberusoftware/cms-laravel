<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ImageProcessingFilament\Resources\ProcessingProfileResource;

final class ListProcessingProfiles extends ListRecords
{
    #[\Override]
    protected static string $resource = ProcessingProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
