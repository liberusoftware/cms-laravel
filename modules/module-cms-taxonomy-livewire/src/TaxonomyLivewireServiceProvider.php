<?php

declare(strict_types=1);

namespace Liberu\Cms\TaxonomyLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\TaxonomyLivewire\Livewire\TaxonomyBrowser;
use Livewire\Livewire;

final class TaxonomyLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-taxonomy.taxonomy-browser', TaxonomyBrowser::class);
    }
}
