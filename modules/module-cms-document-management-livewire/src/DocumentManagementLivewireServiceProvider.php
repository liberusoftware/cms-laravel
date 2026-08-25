<?php

declare(strict_types=1);

namespace Liberu\Cms\DocumentManagementLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\DocumentManagementLivewire\Livewire\DocumentBrowser;
use Livewire\Livewire;

final class DocumentManagementLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-document-management');
        Livewire::component('module-cms-document-management::document-browser', DocumentBrowser::class);
    }
}
