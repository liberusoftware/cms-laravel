<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes;

use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Filament\ContentEntryResource;
use Liberu\Cms\ContentTypes\Filament\ContentTypeResource;
use Liberu\Cms\ContentTypes\Http\Controllers\ContentEntryApiController;
use Liberu\Cms\ContentTypes\Http\Controllers\ContentEntryWriteController;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Preview\ContentEntryPreviewSource;
use Liberu\Cms\ContentTypes\Repositories\ContentEntryRepository;
use Liberu\Cms\ContentTypes\Schema\SchemaValidator;
use Liberu\Cms\ContentTypes\Search\ContentEntrySearchSource;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Contracts\Preview\PreviewRegistryInterface;
use Liberu\Cms\Contracts\Search\SearchRegistryInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContentTypesServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContentTypesModule;
    }

    protected function registerModule(): void
    {
        $this->app->singleton(ContentEntryRepositoryInterface::class, ContentEntryRepository::class);
        $this->app->singleton(SchemaValidator::class);

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $registry = $this->app->make(AdminResourceRegistryInterface::class);
            $registry->registerResource('content-types', ContentTypeResource::class);
            $registry->registerResource('content-types', ContentEntryResource::class);
        }

        if ($this->app->bound(ApiResourceRegistryInterface::class)) {
            $registry = $this->app->make(ApiResourceRegistryInterface::class);
            $registry->registerEndpoint('content-types', new ApiEndpoint('content/{type}', ContentEntryApiController::class, 'index', 'content.index'));
            $registry->registerEndpoint('content-types', new ApiEndpoint('content/{type}/{slug}', ContentEntryApiController::class, 'show', 'content.show'));
            $registry->registerEndpoint('content-types', new ApiEndpoint('content-entries', ContentEntryWriteController::class, 'store', 'content-entries.store', 'POST', ['abilities:content:write']));
            $registry->registerEndpoint('content-types', new ApiEndpoint('content-entries/{id}', ContentEntryWriteController::class, 'update', 'content-entries.update', 'PUT', ['abilities:content:write']));
            $registry->registerEndpoint('content-types', new ApiEndpoint('content-entries/{id}', ContentEntryWriteController::class, 'destroy', 'content-entries.destroy', 'DELETE', ['abilities:content:write']));
        }

        if ($this->app->bound(PreviewRegistryInterface::class)) {
            $this->app->make(PreviewRegistryInterface::class)
                ->registerSource($this->app->make(ContentEntryPreviewSource::class));
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        if ($this->app->bound(AdminDashboardRegistryInterface::class)) {
            $this->app->make(AdminDashboardRegistryInterface::class)->registerStat(
                new DashboardStat('Content entries', fn (): int => ContentEntry::count(), 'heroicon-o-rectangle-stack', 'primary'),
            );
        }

        if ($this->app->bound(SearchRegistryInterface::class)) {
            $this->app->make(SearchRegistryInterface::class)
                ->registerSource($this->app->make(ContentEntrySearchSource::class));
        }
    }
}
