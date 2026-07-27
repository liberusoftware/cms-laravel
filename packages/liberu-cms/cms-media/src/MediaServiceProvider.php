<?php

declare(strict_types=1);

namespace Liberu\Cms\Media;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Media\Filament\MediaResource;
use Liberu\Cms\Media\Media\MediaRepository;
use Liberu\Cms\Media\Media\StoreUpload;
use Liberu\Cms\Media\Models\Media;

final class MediaServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new MediaModule;
    }

    protected function registerModule(): void
    {
        $this->mergeModuleConfig(__DIR__.'/../config/media.php', 'cms-media');

        $this->app->singleton(MediaRepositoryInterface::class, MediaRepository::class);

        $this->app->bind(StoreUpload::class, function (): StoreUpload {
            $config = $this->app->make(ConfigRepository::class);

            $disk = $config->get('cms-media.disk');
            $maxSize = $config->get('cms-media.max_size_kb');
            $mimeTypes = $config->get('cms-media.allowed_mime_types');

            return new StoreUpload(
                $this->app->make(EventBusInterface::class),
                is_string($disk) ? $disk : 'public',
                is_int($maxSize) ? $maxSize : 20480,
                is_array($mimeTypes) ? array_values(array_filter($mimeTypes, is_string(...))) : [],
            );
        });

        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('media', MediaResource::class);
        }
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        if ($this->app->bound(AdminDashboardRegistryInterface::class)) {
            $this->app->make(AdminDashboardRegistryInterface::class)->registerStat(
                new DashboardStat('Media', fn (): int => Media::count(), 'heroicon-o-photo', 'primary'),
            );
        }

        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(
                new PermissionGroup('media', 'Media', AccessScope::Media, ['view', 'update', 'delete']),
            );
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/media.php' => $this->app->configPath('cms-media.php'),
            ], 'cms-media-config');
        }
    }
}
