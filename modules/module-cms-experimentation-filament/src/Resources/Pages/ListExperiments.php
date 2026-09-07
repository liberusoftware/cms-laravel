<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperimentationFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\ExperimentationFilament\Resources\ExperimentResource;

final class ListExperiments extends ListRecords
{
    #[\Override]
    protected static string $resource = ExperimentResource::class;
}
