<?php

declare(strict_types=1);

namespace Liberu\Cms\SeoLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Seo\SeoMetadataService;
use Livewire\Component;

final class SeoCheck extends Component
{
    public string $seoableType = '';

    public int $seoableId = 0;

    public function render(SeoMetadataService $service): View
    {
        return view('cms-seo-livewire::seo-check', ['result' => $this->seoableType === '' || $this->seoableId === 0 ? null : $service->check($this->seoableType, $this->seoableId)]);
    }
}
