<div aria-live="polite">
    @if ($submitted)
        <p>Thank you for your response.</p>
    @elseif ($poll)
        <h2>{{ $poll->title }}</h2>
        @if ($poll->description)<p>{{ $poll->description }}</p>@endif
        @foreach ($poll->questions as $question)
            <div>
                <label for="poll-{{ $question->key }}">{{ $question->prompt }} @if ($question->required)<span aria-hidden="true">*</span>@endif</label>
                @if ($question->type === 'multiple')
                    @foreach ($question->options ?? [] as $option)
                        <label><input type="checkbox" wire:model="answers.{{ $question->key }}" value="{{ $option }}"> {{ $option }}</label>
                    @endforeach
                @elseif ($question->options)
                    <select id="poll-{{ $question->key }}" wire:model="answers.{{ $question->key }}">
                        <option value="">Choose an option</option>
                        @foreach ($question->options as $option)<option value="{{ $option }}">{{ $option }}</option>@endforeach
                    </select>
                @else
                    <input id="poll-{{ $question->key }}" type="{{ $question->type === 'number' ? 'number' : 'text' }}" wire:model="answers.{{ $question->key }}">
                @endif
                @error('answers.'.$question->key)<p role="alert">{{ $message }}</p>@enderror
            </div>
        @endforeach
        <button type="button" wire:click="submit" wire:loading.attr="disabled">Submit</button>
    @else
        <p>Poll unavailable.</p>
    @endif
</div>
