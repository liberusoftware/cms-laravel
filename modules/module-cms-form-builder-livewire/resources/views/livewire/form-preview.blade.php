<div>
    <form wire:submit="submit">
        @foreach ($fields as $field)
            <label wire:key="field-{{ $field['name'] }}">
                {{ $field['label'] ?? $field['name'] }}
                <input type="text" wire:model="input.{{ $field['name'] }}">
            </label>
        @endforeach
        <button type="submit">Submit</button>
    </form>

    @if ($validated !== [])
        <pre>{{ json_encode($validated, JSON_PRETTY_PRINT) }}</pre>
    @endif
</div>
