<?php

declare(strict_types=1);

namespace Liberu\Cms\PollsAndSurveysLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Liberu\Cms\PollsAndSurveys\Models\Poll;
use Liberu\Cms\PollsAndSurveys\Services\PollService;
use Livewire\Component;

final class PollForm extends Component
{
    public string $pollKey = '';

    public array $answers = [];

    public bool $submitted = false;

    public function submit(PollService $service): void
    {
        $poll = Poll::query()->where('key', $this->pollKey)->where('active', true)->firstOrFail();
        $service->submit($poll, $this->answers, auth()->id(), request()->ip());
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('cms-polls-and-surveys-livewire::poll-form', ['poll' => Poll::query()->where('key', $this->pollKey)->where('active', true)->with('questions')->first()]);
    }
}
