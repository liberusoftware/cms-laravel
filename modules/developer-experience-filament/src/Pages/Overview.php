<?php

declare(strict_types=1);

namespace Liberu\Foundation\DeveloperExperienceFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'developer-experience-filament::overview';

    #[\Override]
    protected static ?string $title = 'Developer Experience';
}
