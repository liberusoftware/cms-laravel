<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentLockingFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentLockingFilament\Resources\ContentLockResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentLockingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-locking', ContentLockResource::class);
        }
    }
}
