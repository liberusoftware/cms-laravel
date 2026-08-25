<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTypes\Queries\FieldSchemaQuery;
use Liberu\Cms\FieldSystemLivewire\Livewire\SchemaBrowser;
use Livewire\Livewire;

final class FieldSystemLivewireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FieldSchemaQuery::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'cms-field-system-livewire');
        Livewire::component('module-cms-field-system.schema-browser', SchemaBrowser::class);
    }
}
