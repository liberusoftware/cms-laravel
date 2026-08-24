<?php

declare(strict_types=1);

namespace Liberu\Cms\SyndicationAndFeedsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\SyndicationAndFeedsLivewire\Livewire\FeedPreview;
use Livewire\Livewire;

final class SyndicationAndFeedsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-syndication-and-feeds.feed-preview', FeedPreview::class);
    }
}
