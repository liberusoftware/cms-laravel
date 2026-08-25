<?php

declare(strict_types=1);

namespace Liberu\Cms\PublishingFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\PublishingFilament\Resources\PublicationReleaseResource;

final class PublishingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('publishing', PublicationReleaseResource::class);
        }
    }
}
