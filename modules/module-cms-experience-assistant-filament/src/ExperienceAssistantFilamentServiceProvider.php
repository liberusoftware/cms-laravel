<?php

declare(strict_types=1);

namespace Liberu\Cms\ExperienceAssistantFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\ExperienceAssistantFilament\Resources\ExperienceSuggestionResource;

final class ExperienceAssistantFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('experience-assistant', ExperienceSuggestionResource::class);
        }
    }
}
