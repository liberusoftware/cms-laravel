<div>
    @if ($submitted)<p>Thank you for your response.</p>@elseif ($poll)
        <h2>{{ $poll->title }}</h2>
        @foreach ($poll->questions as $question)<label>{{ $question->prompt }}<input wire:model="answers.{{ $question->key }}"></label>@endforeach
        <button type="button" wire:click="submit">Submit</button>
    @else <p>Poll unavailable.</p>@endif
</div>
