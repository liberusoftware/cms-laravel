<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\SitemapsFilament\Resources\SitemapEntryResource;

final class SitemapsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('sitemaps', SitemapEntryResource::class);
        }
    }
}
