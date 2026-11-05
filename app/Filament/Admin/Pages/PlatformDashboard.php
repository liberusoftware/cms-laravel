<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Pages\Workspace;

class PlatformDashboard extends Workspace
{
    protected static ?string $title = 'Platform overview';

    public static function canAccess(): bool
    {
        return PlatformSetup::canAccess();
    }

    public function getSubheading(): ?string
    {
        return __('Shared platform settings and a separate workspace for every website team.');
    }
}
