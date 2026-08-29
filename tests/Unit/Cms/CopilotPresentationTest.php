<?php

declare(strict_types=1);

use Liberu\Cms\CopilotFilament\Resources\CopilotRequestResource;
use Liberu\Cms\CopilotLivewire\Livewire\CopilotConsole;
use Livewire\Livewire;

it('registers Copilot Filament and Livewire surfaces', function (): void {
    expect(CopilotRequestResource::getSlug())->toBe('cms-copilot-requests')
        ->and(app('livewire')->exists('module-cms-copilot.console'))->toBeTrue();
    Livewire::test(CopilotConsole::class)->assertSee('Ready');
});
