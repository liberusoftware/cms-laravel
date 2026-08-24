<?php

declare(strict_types=1);

namespace Liberu\Cms\Posts\Filament\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Posts\Filament\PostResource;

final class ListPosts extends ListRecords
{
    #[\Override]
    protected static string $resource = PostResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
