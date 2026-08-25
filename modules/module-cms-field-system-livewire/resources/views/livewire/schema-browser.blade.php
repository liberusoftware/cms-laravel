<div>
    @if ($schema)
        <h2>{{ $schema['key'] }} (v{{ $schema['version'] }})</h2>
        <ul aria-label="Field schema">
            @foreach ($schema['fields'] as $field)
                <li wire:key="field-{{ $field['name'] }}">{{ $field['label'] }} ({{ $field['type'] }})</li>
            @endforeach
        </ul>
    @else
        <p>Schema not found.</p>
    @endif
</div>
