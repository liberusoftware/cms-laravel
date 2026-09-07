<?php

declare(strict_types=1);

namespace Liberu\Foundation\TwoFactorAuthenticationFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'two-factor-authentication-filament::overview';

    #[\Override]
    protected static ?string $title = 'Two-Factor Authentication';
}
