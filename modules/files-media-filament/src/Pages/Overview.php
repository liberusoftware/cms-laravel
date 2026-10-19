<?php

declare(strict_types=1);

namespace Liberu\Foundation\FilesMediaFilament\Pages;

use Filament\Pages\Page;

final class Overview extends Page
{
    #[\Override]
    protected string $view = 'files-media-filament::overview';

    #[\Override]
    protected static ?string $title = 'Files and Media';
}
