<?php

declare(strict_types=1);

namespace Liberu\Cms\RevisionsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\RevisionsLivewire\Livewire\RevisionHistory;
use Livewire\Livewire;

final class RevisionsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-revisions.revision-history', RevisionHistory::class);
    }
}
