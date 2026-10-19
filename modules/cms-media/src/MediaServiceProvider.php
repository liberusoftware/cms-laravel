<?php

declare(strict_types=1);

namespace Liberu\Cms\Media;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Admin\AdminDashboardRegistryInterface;
use Liberu\Cms\Contracts\Admin\DashboardStat;
use Liberu\Cms\Contracts\Events\EventBusInterface;
use Liberu\Cms\Contracts\Health\HealthCheckRegistryInterface;
use Liberu\Cms\Contracts\Media\MediaRepositoryInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\Media\Health\StorageHealthCheck;
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

    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');

        $this->registerHealthCheck();

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

    /**
     * Contribute the media disk's writability probe to the readiness registry,
     * when observability is present. Criticality is owned by this module's own
     * config (defaulting to degraded), so nothing here reaches into the
     * observability module.
     */
    private function registerHealthCheck(): void
    {
        if (! $this->app->bound(HealthCheckRegistryInterface::class)) {
            return;
        }

        $config = $this->app->make(ConfigRepository::class);
        $disk = $config->get('cms-media.disk');

        $this->app->make(HealthCheckRegistryInterface::class)->register(new StorageHealthCheck(
            $this->app->make(FilesystemFactory::class),
            is_string($disk) ? $disk : 'public',
            (bool) $config->get('cms-media.readiness.critical', false),
        ));
    }
}
