<?php

declare(strict_types=1);

namespace Liberu\Cms\ContentTemplatesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\ContentTemplates\Services\ContentTemplatesService;
use Livewire\Component;

final class TemplatePicker extends Component
{
    public string $contentType = 'page';

    public function render(): View
    {
        return view('module-cms-content-templates::template-picker', ['templates' => app(ContentTemplatesService::class)->templates(auth()->user()?->current_team_id, $this->contentType)]);
    }
}
