<?php

declare(strict_types=1);

namespace Liberu\Cms\DrupalMigrationFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\DrupalMigrationFilament\Resources\DrupalMigrationResource;

final class DrupalMigrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('drupal-migration', DrupalMigrationResource::class);
        }
    }
}
