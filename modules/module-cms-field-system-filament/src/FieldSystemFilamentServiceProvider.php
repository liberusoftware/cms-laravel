<?php

declare(strict_types=1);

namespace Liberu\Cms\FieldSystemFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\FieldSystemFilament\Resources\FieldSchemaResource;

final class FieldSystemFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('field-system', FieldSchemaResource::class);
        }
    }
}
