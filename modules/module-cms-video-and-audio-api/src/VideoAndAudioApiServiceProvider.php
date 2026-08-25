<?php

declare(strict_types=1);

namespace Liberu\Cms\VideoAndAudioApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\VideoAndAudioApi\Http\Controllers\VideoAndAudioController;

final class VideoAndAudioApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets', VideoAndAudioController::class, 'index', 'cms.video-and-audio.assets'));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets/{publicId}', VideoAndAudioController::class, 'show', 'cms.video-and-audio.asset'));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets', VideoAndAudioController::class, 'create', 'cms.video-and-audio.assets.create', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets/{publicId}', VideoAndAudioController::class, 'update', 'cms.video-and-audio.asset.update', 'PATCH', ['abilities:content:write']));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets/{publicId}/archive', VideoAndAudioController::class, 'archive', 'cms.video-and-audio.asset.archive', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets/{publicId}/tracks', VideoAndAudioController::class, 'track', 'cms.video-and-audio.track', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets/{publicId}/transcode', VideoAndAudioController::class, 'transcode', 'cms.video-and-audio.transcode', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('video-and-audio-api', new ApiEndpoint('cms/video-and-audio/assets/{publicId}/playback', VideoAndAudioController::class, 'playback', 'cms.video-and-audio.playback'));
    }
}
