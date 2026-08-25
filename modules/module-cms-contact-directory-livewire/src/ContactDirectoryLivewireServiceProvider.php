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
        Livewire::component('module-cms-contact-directory::contact-directory', ContactDirectory::class);
    }
}
