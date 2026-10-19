<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentEntitiesFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentEntitiesFilament\Fields\DefaultFilamentFieldTypes;
use Liberu\Cms\ContentEntitiesFilament\Resources\ContentBundleResource;
use Liberu\Cms\ContentEntitiesFilament\Resources\ContentEntityResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;

final class ContentEntitiesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(FieldTypeRegistryInterface::class)) {
            DefaultFilamentFieldTypes::registerInto($this->app->make(FieldTypeRegistryInterface::class));
        }

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $registry = $this->app->make(AdminResourceRegistryInterface::class);
            $registry->registerResource('content-entities', ContentBundleResource::class);
            $registry->registerResource('content-entities', ContentEntityResource::class);
        }
    }
}
