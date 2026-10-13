<?php

declare(strict_types=1);

namespace Liberu\Foundation\LocalizationCoreFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'localization-core-filament::overview';

    #[\Override]
    protected static ?string $title = 'Localization';
}
