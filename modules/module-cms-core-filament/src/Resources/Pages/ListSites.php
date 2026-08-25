<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\CoreFilament\Resources\SiteResource;

final class ListSites extends ListRecords
{
    #[\Override]
    protected static string $resource = SiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn (array $data) => app(CoreMutationService::class)->createSite($data)),
        ];
    }
}
