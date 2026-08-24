<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Cms\SitemapsLivewire\Livewire\SitemapPreview;
use Livewire\Livewire;

final class SitemapsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('module-cms-sitemaps.sitemap-preview', SitemapPreview::class);
    }
}
