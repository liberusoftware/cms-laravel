<?php

declare(strict_types=1);

namespace Liberu\Foundation\SearchFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'search-filament::overview';

    #[\Override]
    protected static ?string $title = 'Search';
}
