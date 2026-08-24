<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\SeoFilament\Resources\SeoMetadataResource;

final class SeoFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('seo', SeoMetadataResource::class);
        }
    }
}
