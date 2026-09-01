<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsGoogleFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'analytics-google-filament::overview';

    #[\Override]
    protected static ?string $title = 'Google Analytics';
}
