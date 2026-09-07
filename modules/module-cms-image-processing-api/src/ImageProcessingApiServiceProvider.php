<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingApi;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Api\ApiEndpoint;
use Liberu\Cms\Contracts\Api\ApiResourceRegistryInterface;
use Liberu\Cms\ImageProcessingApi\Http\ImageProcessingController;

final class ImageProcessingApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->bound(ApiResourceRegistryInterface::class)) {
            return;
        }
        $registry = $this->app->make(ApiResourceRegistryInterface::class);
        $registry->registerEndpoint('image-processing-api', new ApiEndpoint('cms/image-processing/profiles', ImageProcessingController::class, 'profiles', 'cms.image-processing.profiles'));
        $registry->registerEndpoint('image-processing-api', new ApiEndpoint('cms/image-processing/profiles', ImageProcessingController::class, 'storeProfile', 'cms.image-processing.profile.store', 'POST', ['abilities:content:write']));
        $registry->registerEndpoint('image-processing-api', new ApiEndpoint('cms/image-processing/profiles/{key}/derivatives', ImageProcessingController::class, 'storeDerivative', 'cms.image-processing.derivative.store', 'POST', ['abilities:content:write']));
    }
}
