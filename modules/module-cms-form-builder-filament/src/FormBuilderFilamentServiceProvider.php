<?php

declare(strict_types=1);

namespace Liberu\Cms\FormBuilderFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\FormBuilderFilament\Pages\FormBuilderOverview;

final class FormBuilderFilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-form-builder-filament');
    }

    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerPage('form-builder', FormBuilderOverview::class);
        }
    }
}
