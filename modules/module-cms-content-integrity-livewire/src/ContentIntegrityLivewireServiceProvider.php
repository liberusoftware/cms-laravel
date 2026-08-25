<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentIntegrityLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\ContentIntegrityLivewire\Livewire\IntegrityReport;
use Livewire\Livewire;

final class ContentIntegrityLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-content-integrity::integrity-report', IntegrityReport::class);
    }
}
