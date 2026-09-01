<?php

declare(strict_types=1);

namespace Liberu\Cms\MediaAssistantFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\MediaAssistantFilament\Resources\MediaSuggestionResource;

final class MediaAssistantFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('media-assistant', MediaSuggestionResource::class);
        }
    }
}
