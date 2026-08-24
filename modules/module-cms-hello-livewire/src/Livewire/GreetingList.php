<?php

declare(strict_types=1);

namespace Liberu\Cms\HelloLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\Hello\Models\Greeting;
use Livewire\Component;
use Livewire\WithPagination;

final class GreetingList extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public function updatedPerPage(int|string $value): void
    {
        $this->perPage = max(1, min(50, (int) $value));
        $this->resetPage();
    }

    public function render(): View
    {
        return view('cms-hello-livewire::livewire.greeting-list', [
            'greetings' => Greeting::query()->latest('id')->paginate($this->perPage),
        ]);
    }
}
