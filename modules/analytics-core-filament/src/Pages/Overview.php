<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsCoreFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'analytics-core-filament::overview';

    #[\Override]
    protected static ?string $title = 'Analytics Core';
}
