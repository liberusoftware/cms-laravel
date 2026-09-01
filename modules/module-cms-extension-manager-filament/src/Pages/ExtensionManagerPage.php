<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManagerFilament\Pages;

use Filament\Pages\Page;
use Liberu\Cms\ExtensionManager\Services\ExtensionManagerService;

final class ExtensionManagerPage extends Page
{
    #[\Override]
    protected string $view = 'module-cms-extension-manager::extension-manager';

    #[\Override]
    protected static ?string $title = 'Extensions';

    public function extensions(): array
    {
        return app(ExtensionManagerService::class)->all();
    }
}
