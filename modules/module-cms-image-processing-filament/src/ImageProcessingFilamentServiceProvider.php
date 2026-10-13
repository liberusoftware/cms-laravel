<?php

declare(strict_types=1);

namespace Liberu\Cms\ImageProcessingFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ImageProcessingFilament\Resources\ProcessingProfileResource;

final class ImageProcessingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('image-processing', ProcessingProfileResource::class);
        }
    }
}
