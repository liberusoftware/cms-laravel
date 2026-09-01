<?php

declare(strict_types=1);

namespace Liberu\Cms\CopilotLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Copilot\Services\CopilotService;
use Livewire\Component;

final class CopilotConsole extends Component
{
    public string $capability = 'summary';

    public string $prompt = '';

    /** @var array<string, mixed> */
    public array $result = [];

    public function submit(CopilotService $service): void
    {
        $request = $service->submit(auth()->user()?->current_team_id, $this->capability, $this->prompt);
        $this->result = ['request_id' => $request->getKey(), 'status' => $request->status];
    }

    public function render(): View
    {
        return view('module-cms-copilot-livewire::console');
    }
}
