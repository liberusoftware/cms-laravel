<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\VideoAndAudioFilament\Resources\MediaAssetResource;

final class VideoAndAudioFilamentServiceProvider extends ServiceProvider
{
    public function register(): void { if ($this->app->bound(AdminResourceRegistryInterface::class)) $this->app->make(AdminResourceRegistryInterface::class)->registerResource('video-and-audio', MediaAssetResource::class); }
}
