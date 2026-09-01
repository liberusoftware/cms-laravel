<?php

declare(strict_types=1);

namespace Liberu\Cms\DisplayModesLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\DisplayModes\Services\DisplayModesService;
use Livewire\Component;

final class ModePicker extends Component
{
    public string $contentType = 'page';

    public string $slug = 'default';

    public function render(): View
    {
        return view('module-cms-display-modes::mode-picker', ['mode' => app(DisplayModesService::class)->select($this->contentType, auth()->user()?->current_team_id, $this->slug)]);
    }
}
