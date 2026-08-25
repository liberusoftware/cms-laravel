<?php

declare(strict_types=1);

namespace Liberu\Cms\MultisiteFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\MultisiteFilament\Resources\MultisiteSiteResource;

final class MultisiteFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('multisite', MultisiteSiteResource::class);
        }
    }
}
