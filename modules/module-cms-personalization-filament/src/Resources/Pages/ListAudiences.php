<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\PersonalizationFilament\Resources\AudienceResource;

final class ListAudiences extends ListRecords
{
    #[\Override]
    protected static string $resource = AudienceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
