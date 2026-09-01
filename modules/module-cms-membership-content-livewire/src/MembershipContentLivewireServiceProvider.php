<?php

declare(strict_types=1);

namespace Liberu\Cms\MembershipContentLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\MembershipContentLivewire\Livewire\MembershipContentBrowser;
use Livewire\Livewire;

final class MembershipContentLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-cms-membership-content-livewire');
        Livewire::component('module-cms-membership-content::browser', MembershipContentBrowser::class);
    }
}
