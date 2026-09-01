<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\MediaLibraryFilament\Resources\MediaResource;

final class MediaLibraryFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(AdminResourceRegistryInterface::class)) {
            return;
        }

        $this->app->make(AdminResourceRegistryInterface::class)->registerResource('media-library', MediaResource::class);
    }
}
