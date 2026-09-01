<?php

declare(strict_types=1);

namespace Liberu\Foundation\WebhooksFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'webhooks-filament::overview';

    #[\Override]
    protected static ?string $title = 'Webhooks';
}
