<?php

declare(strict_types=1);

namespace Liberu\Foundation\ApplicationFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'application-filament::overview';

    #[\Override]
    protected static ?string $title = 'Application Core';
}
