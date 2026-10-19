<?php

declare(strict_types=1);

namespace Liberu\Foundation\ImportExportFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'import-export-filament::overview';

    #[\Override]
    protected static ?string $title = 'Import and Export';
}
