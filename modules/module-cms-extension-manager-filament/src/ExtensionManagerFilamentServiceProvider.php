<?php

declare(strict_types=1);

namespace Liberu\Cms\ExtensionManagerFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ExtensionManagerFilament\Pages\ExtensionManagerPage;

final class ExtensionManagerFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerPage('extension-manager', ExtensionManagerPage::class);
        }
    }
}
