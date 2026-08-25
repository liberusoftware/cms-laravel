<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTemplatesFilament\Resources\ContentTemplateResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContentTemplatesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('content-templates', ContentTemplateResource::class);
        }
    }
}
