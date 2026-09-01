<?php

declare(strict_types=1);

namespace Liberu\Cms\AuditAndHistoryFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\Cms\AuditAndHistoryFilament\Resources\AuditLogResource;

final class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;
}
