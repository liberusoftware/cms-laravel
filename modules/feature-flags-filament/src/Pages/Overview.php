<?php

declare(strict_types=1);

namespace Liberu\Foundation\FeatureFlagsFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'feature-flags-filament::overview';

    #[\Override]
    protected static ?string $title = 'Feature Flags';
}
