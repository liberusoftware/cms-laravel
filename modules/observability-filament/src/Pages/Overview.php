<?php

declare(strict_types=1);

namespace Liberu\Foundation\ObservabilityFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'observability-filament::overview';

    #[\Override]
    protected static ?string $title = 'Observability';
}
