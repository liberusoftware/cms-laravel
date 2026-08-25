<?php

declare(strict_types=1);

namespace Liberu\Cms\PersonalizationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Personalization\Services\DecisionEngine;
use Livewire\Component;

final class AudiencePreview extends Component
{
    public string $audience = '';

    public string $subject = '';

    public function render(): View
    {
        return view('cms-personalization-livewire::audience-preview', ['decision' => $this->audience === '' ? null : app(DecisionEngine::class)->decide($this->audience, [], $this->subject ?: null, true)]);
    }
}
