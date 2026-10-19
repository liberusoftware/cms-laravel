<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTypes;

use Liberu\Cms\ContentTypes\Actions\ContentEntryMutationService;
use Liberu\Cms\ContentTypes\Contracts\ContentEntryRepositoryInterface;
use Liberu\Cms\ContentTypes\Fields\DefaultFieldTypes;
use Liberu\Cms\ContentTypes\Fields\FieldTypeRegistry;
use Liberu\Cms\ContentTypes\Models\ContentEntry;
use Liberu\Cms\ContentTypes\Preview\ContentEntryPreviewSource;
use Liberu\Cms\ContentTypes\Queries\FieldSchemaQuery;
use Liberu\Cms\ContentTypes\Queries\PublishedEntityQuery;
use Liberu\Cms\ContentTypes\Repositories\ContentEntryRepository;
use Liberu\Cms\ContentTypes\Schema\SchemaValidator;
use Liberu\Cms\ContentTypes\Search\ContentEntrySearchSource;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Fields\FieldTypeRegistryInterface;
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
        $this->app->singleton(ContentEntryMutationService::class);
        $this->app->singleton(ContentEntryRepositoryInterface::class, ContentEntryRepository::class);

        $this->app->singleton(FieldTypeRegistryInterface::class, FieldTypeRegistry::class);
        DefaultFieldTypes::registerInto($this->app->make(FieldTypeRegistryInterface::class));

        $this->app->singleton(SchemaValidator::class);
        $this->app->singleton(FieldSchemaQuery::class);
        $this->app->singleton(PublishedEntityQuery::class);

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

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $registrar = $this->app->make(PermissionRegistrarInterface::class);
            $registrar->register(new PermissionGroup('content-types', 'Content types', AccessScope::Content, ['view', 'create', 'update', 'delete']));
            $registrar->register(new PermissionGroup('content-entries', 'Content entries', AccessScope::Content, ['view', 'create', 'update', 'delete']));
        }
    }
}
