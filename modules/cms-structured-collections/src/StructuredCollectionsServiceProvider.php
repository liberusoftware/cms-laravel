<?php

declare(strict_types=1);

namespace Liberu\Cms\StructuredCollections;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\StructuredCollections\Actions\StructuredCollectionMutationService;
use Liberu\Cms\StructuredCollections\Queries\StructuredCollectionQuery;

/**
 * Canonical package entry point. The legacy collections package owns the
 * persistence and public boundaries; this package supplies the roadmap name
 * without creating a second set of tables or competing model classes.
 */
final class StructuredCollectionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StructuredCollectionQuery::class);
        $this->app->singleton(StructuredCollectionMutationService::class);
    }
}
