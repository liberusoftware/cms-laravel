<?php

declare(strict_types=1);

namespace Liberu\Cms\RelatedContentLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\RelatedContentLivewire\Livewire\RelatedContentList;
use Livewire\Livewire;

final class RelatedContentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-related-content.related-content-list', RelatedContentList::class);
    }
}
