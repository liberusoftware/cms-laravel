<?php

declare(strict_types=1);

namespace Liberu\Cms\MetadataFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\MetadataFilament\Resources\MetadataEntryResource;

final class MetadataFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('metadata', MetadataEntryResource::class);
        }
    }
}
