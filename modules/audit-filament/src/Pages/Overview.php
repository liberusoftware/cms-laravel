<?php

declare(strict_types=1);

namespace Liberu\Foundation\AuditFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'audit-filament::overview';

    #[\Override]
    protected static ?string $title = 'Audit';
}
