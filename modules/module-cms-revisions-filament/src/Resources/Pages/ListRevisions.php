<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\RevisionsFilament\Resources\RevisionResource;

final class ListRevisions extends ListRecords
{
    #[\Override]
    protected static string $resource = RevisionResource::class;
}
