<?php

declare(strict_types=1);

namespace Liberu\Cms\AccessibilityAssistantLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\AccessibilityAssistant\Services\AccessibilityAssistantService;
use Livewire\Component;

final class AccessibilityAnalyzer extends Component
{
    public string $html = '';

    /** @var array<int, array{code:string, severity:string, message:string}> */
    public array $findings = [];

    public function updatedHtml(AccessibilityAssistantService $service): void
    {
        $this->findings = trim($this->html) === '' ? [] : $service->analyze($this->html);
    }

    public function render(): View
    {
        return view('module-cms-accessibility-assistant-livewire::analyzer');
    }
}
