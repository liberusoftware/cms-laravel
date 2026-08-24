<?php

declare(strict_types=1);

namespace Liberu\Cms\SiteFactoryFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\SiteFactoryFilament\Resources\SiteTemplateResource;

final class SiteFactoryFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('site-factory', SiteTemplateResource::class);
        }
    }
}
