<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\SecurityOperationsFilament\Resources\SecurityOperationResource;

final class ListSecurityOperations extends ListRecords
{
    protected static string $resource = SecurityOperationResource::class;
}
