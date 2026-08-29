<?php

declare(strict_types=1);

namespace Liberu\Cms\IntegrationDirectoryFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\IntegrationDirectoryFilament\Resources\IntegrationResource;

final class IntegrationDirectoryFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('integration-directory', IntegrationResource::class);
        }
    }
}
