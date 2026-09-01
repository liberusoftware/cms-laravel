<?php

declare(strict_types=1);

namespace Liberu\Foundation\JetstreamBridgeFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'jetstream-bridge-filament::overview';

    #[\Override]
    protected static ?string $title = 'Jetstream Bridge';
}
