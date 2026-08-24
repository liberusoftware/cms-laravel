<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTypes\Filament\ContentEntryResource;
use Liberu\Cms\ContentTypes\Filament\ContentTypeResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentEntitiesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $registry = $this->app->make(AdminResourceRegistryInterface::class);
            $registry->registerResource('content-entities', ContentTypeResource::class);
            $registry->registerResource('content-entities', ContentEntryResource::class);
        }
    }
}
