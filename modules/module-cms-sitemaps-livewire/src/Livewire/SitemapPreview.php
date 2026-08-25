<?php

declare(strict_types=1);

namespace Liberu\Cms\SitemapsLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Sitemaps\Services\SitemapService;
use Livewire\Component;

final class SitemapPreview extends Component
{
    public ?int $siteId = null;

    public function render(SitemapService $service): View
    {
        return view('cms-sitemaps-livewire::sitemap-preview', ['entries' => $service->entries($this->siteId)]);
    }
}
