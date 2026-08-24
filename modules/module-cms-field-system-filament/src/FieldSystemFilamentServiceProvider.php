<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentTypes\Filament\ContentTypeResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class FieldSystemFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('field-system', ContentTypeResource::class);
        }
    }
}
