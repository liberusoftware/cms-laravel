<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudio;

use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;
use Liberu\Cms\VideoAndAudio\Actions\MediaManagementService;
use Liberu\Cms\VideoAndAudio\Queries\MediaAssetQuery;
use Liberu\Cms\VideoAndAudio\Support\TranscodingAdapterRegistry;

final class VideoAndAudioServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new VideoAndAudioModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/video-and-audio.php', 'video-and-audio');
        $this->app->singleton(TranscodingAdapterRegistry::class);
        $this->app->singleton(MediaManagementService::class);
        $this->app->singleton(MediaAssetQuery::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('video-and-audio', 'Video and Audio', AccessScope::Content, ['view', 'create', 'update', 'delete', 'transcode', 'publish']));
        }
    }
}
