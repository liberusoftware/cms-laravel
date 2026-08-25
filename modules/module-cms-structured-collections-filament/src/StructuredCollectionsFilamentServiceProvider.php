<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollectionsFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\StructuredCollectionsFilament\Resources\StructuredCollectionRecordResource;
use Liberu\Cms\StructuredCollectionsFilament\Resources\StructuredCollectionResource;

/** The legacy collections provider remains the single resource owner. */
final class StructuredCollectionsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(AdminResourceRegistryInterface::class)) return;
        $r = $this->app->make(AdminResourceRegistryInterface::class);
        $r->registerResource('structured-collections', StructuredCollectionResource::class);
        $r->registerResource('structured-collection-records', StructuredCollectionRecordResource::class);
    }
}
