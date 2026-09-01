<?php

declare(strict_types=1);

namespace Liberu\Foundation\CurrencyContextFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'currency-context-filament::overview';

    #[\Override]
    protected static ?string $title = 'Currency Context';
}
