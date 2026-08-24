<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysFilament\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\PollsAndSurveysFilament\Resources\PollResource;

final class ListPolls extends ListRecords
{
    protected static string $resource = PollResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
