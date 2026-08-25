<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationAssistantFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\TranslationAssistantFilament\Resources\TranslationDraftResource;

final class TranslationAssistantFilamentServiceProvider extends ServiceProvider
{
    public function register(): void { if ($this->app->bound(AdminResourceRegistryInterface::class)) { $this->app->make(AdminResourceRegistryInterface::class)->registerResource('translation-assistant', TranslationDraftResource::class); } }
}
