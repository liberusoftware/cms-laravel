<?php

declare(strict_types=1);

namespace Liberu\Cms\TranslationManagementFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;
use Liberu\Cms\TranslationManagementFilament\Resources\TranslationJobResource;

final class TranslationManagementFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('translation-management', TranslationJobResource::class);
        }
    }
}
