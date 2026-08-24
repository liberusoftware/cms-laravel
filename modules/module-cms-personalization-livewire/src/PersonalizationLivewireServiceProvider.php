<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\PersonalizationLivewire\Livewire\AudiencePreview;
use Livewire\Livewire;

final class PersonalizationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-personalization.audience-preview', AudiencePreview::class);
    }
}
