<?php

declare(strict_types=1);

namespace Liberu\Cms\RedirectsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\RedirectsFilament\Resources\RedirectResource;

final class RedirectsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('redirects', RedirectResource::class);
        }
    }
}
