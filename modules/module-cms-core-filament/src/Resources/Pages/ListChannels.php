<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Core\Actions\CoreMutationService;
use Liberu\Cms\CoreFilament\Resources\ChannelResource;

final class ListChannels extends ListRecords
{
    #[\Override]
    protected static string $resource = ChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(fn (array $data) => app(CoreMutationService::class)->createChannel($data['site_id'], $data)),
        ];
    }
}
