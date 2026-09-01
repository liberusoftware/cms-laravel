<?php

declare(strict_types=1);

namespace Liberu\Cms\ContactDirectoryLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContactDirectoryLivewire\Livewire\ContactDirectory;
use Livewire\Livewire;

final class ContactDirectoryLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-contact-directory');
        Livewire::component('module-cms-contact-directory::contact-directory', ContactDirectory::class);
    }
}
