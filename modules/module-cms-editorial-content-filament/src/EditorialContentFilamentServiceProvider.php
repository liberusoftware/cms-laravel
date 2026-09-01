<?php

declare(strict_types=1);

namespace Liberu\Cms\EditorialContentFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\EditorialContentFilament\Resources\EditorialPostResource;

final class EditorialContentFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('editorial-content', EditorialPostResource::class);
        }
    }
}
