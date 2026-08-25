<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\Cms\PollsAndSurveysFilament\Resources\PollResource;

final class EditPoll extends EditRecord
{
    #[\Override]
    protected static string $resource = PollResource::class;
}
