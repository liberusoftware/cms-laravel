<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectory;

use Liberu\Cms\ContactDirectory\Services\ContactDirectoryService;
use Liberu\Cms\Contracts\Access\AccessScope;
use Liberu\Cms\Contracts\Access\PermissionGroup;
use Liberu\Cms\Contracts\Access\PermissionRegistrarInterface;
use Liberu\Cms\Contracts\Module\ModuleInterface;
use Liberu\Cms\Core\Module\ModuleServiceProvider;

final class ContactDirectoryServiceProvider extends ModuleServiceProvider
{
    public function module(): ModuleInterface
    {
        return new ContactDirectoryModule;
    }

    protected function registerModule(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/contact-directory.php', 'contact-directory');
        $this->app->singleton(ContactDirectoryService::class);
    }

    protected function bootModule(): void
    {
        $this->loadModuleMigrations(__DIR__.'/../database/migrations');
        if ($this->app->bound(PermissionRegistrarInterface::class)) {
            $this->app->make(PermissionRegistrarInterface::class)->register(new PermissionGroup('contact-directory', 'Contact Directory', AccessScope::Module, ['view', 'create', 'update', 'delete']));
        }
    }
}
