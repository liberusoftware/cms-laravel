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

    public bool $consent = false;

    public array $context = [];

    public function mount(?string $audience = null): void
    {
        if ($audience !== null) {
            $this->audience = $audience;
        }
    }

    public function preview(): void
    {
        $this->validate(['audience' => ['required', 'string', 'max:120'], 'subject' => ['nullable', 'string', 'max:255'], 'context' => ['array'], 'consent' => ['boolean']]);
    }

    public function render(): View
    {
        return view('cms-personalization-livewire::audience-preview', ['decision' => $this->audience === '' ? null : app(DecisionEngine::class)->decide($this->audience, $this->context, $this->subject ?: null, $this->consent)]);
    }
}
