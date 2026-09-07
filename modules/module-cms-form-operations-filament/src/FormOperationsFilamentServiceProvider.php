<?php

declare(strict_types=1);

namespace Liberu\Cms\FormOperationsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\FormOperationsFilament\Pages\FormOperationsOverview;

final class FormOperationsFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-form-operations-filament');
    }

    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerPage('form-operations', FormOperationsOverview::class);
        }
    }
}
