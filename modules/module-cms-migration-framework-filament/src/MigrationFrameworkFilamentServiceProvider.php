<?php

declare(strict_types=1);

namespace Liberu\Cms\MigrationFrameworkFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\MigrationFrameworkFilament\Resources\MigrationJobResource;

final class MigrationFrameworkFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('migration-framework', MigrationJobResource::class);
        }
    }
}
