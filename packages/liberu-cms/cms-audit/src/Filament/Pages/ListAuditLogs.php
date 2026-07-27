<?php

declare(strict_types=1);

namespace Liberu\Cms\Audit\Filament\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\Audit\Filament\AuditLogResource;

final class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;
}
