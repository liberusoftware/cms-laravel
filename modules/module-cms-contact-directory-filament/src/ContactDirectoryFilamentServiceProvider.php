<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryFilament;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContactDirectoryFilament\Resources\ContactResource;
use Liberu\Cms\Contracts\Admin\AdminResourceRegistryInterface;

final class ContactDirectoryFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->bound(AdminResourceRegistryInterface::class)) {
            $this->app->make(AdminResourceRegistryInterface::class)->registerResource('contact-directory', ContactResource::class);
        }
    }
}
