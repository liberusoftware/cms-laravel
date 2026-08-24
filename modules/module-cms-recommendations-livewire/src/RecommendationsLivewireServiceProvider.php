<?php

declare(strict_types=1);

namespace Liberu\Cms\RecommendationsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\RecommendationsLivewire\Livewire\RecommendationList;
use Livewire\Livewire;

final class RecommendationsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-recommendations.recommendation-list', RecommendationList::class);
    }
}
