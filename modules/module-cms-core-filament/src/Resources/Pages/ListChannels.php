<?php

declare(strict_types=1);

namespace Liberu\Cms\CoreFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use InvalidArgumentException;
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
                ->using(
                    /** @param array<string, mixed> $data */
                    fn (array $data) => app(CoreMutationService::class)->createChannel(
                        is_int($data['site_id'] ?? null) || is_string($data['site_id'] ?? null)
                            ? $data['site_id']
                            : throw new InvalidArgumentException('A valid site is required.'),
                        $data,
                    ),
                ),
        ];
    }
}
