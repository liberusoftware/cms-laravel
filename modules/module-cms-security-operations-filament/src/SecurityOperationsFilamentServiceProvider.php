<?php

declare(strict_types=1);

namespace Liberu\Cms\SecurityOperationsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\SecurityOperationsFilament\Resources\SecurityOperationResource;

final class SecurityOperationsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('security-operations', SecurityOperationResource::class);
        }
    }
}
