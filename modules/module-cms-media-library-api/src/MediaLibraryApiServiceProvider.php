<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaLibraryApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\MediaLibraryApi\Http\MediaLibraryController;

final class MediaLibraryApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }

        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('media-library-api', new ApiEndpoint(
            'cms/media-library', MediaLibraryController::class, 'index', 'cms.media-library.index', 'GET',
        ));
        $registry->registerEndpoint('media-library-api', new ApiEndpoint(
            'cms/media-library/{media}', MediaLibraryController::class, 'show', 'cms.media-library.show', 'GET',
        ));
        $registry->registerEndpoint('media-library-api', new ApiEndpoint(
            'cms/media-library/upload', MediaLibraryController::class, 'upload', 'cms.media-library.upload', 'POST', ['abilities:media:write'],
        ));
        $registry->registerEndpoint('media-library-api', new ApiEndpoint(
            'cms/media-library/{media}', MediaLibraryController::class, 'destroy', 'cms.media-library.destroy', 'DELETE', ['abilities:media:write'],
        ));
    }
}
