<?php

declare(strict_types=1);

namespace Liberu\Cms\HeadlessApiFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\HeadlessApiFilament\Resources\PersistedQueryResource;

final class HeadlessApiFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('headless-api', PersistedQueryResource::class);
        }
    }
}
