<?php

declare(strict_types=1);

namespace Liberu\Foundation\AnalyticsMetaFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'analytics-meta-filament::overview';

    #[\Override]
    protected static ?string $title = 'Meta Server-Side Tracking';
}
