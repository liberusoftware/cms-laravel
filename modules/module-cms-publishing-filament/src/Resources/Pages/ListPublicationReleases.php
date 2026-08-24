<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\PublishingFilament\Resources\PublicationReleaseResource;

final class ListPublicationReleases extends ListRecords
{
    protected static string $resource = PublicationReleaseResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
