<div>
    @if ($contentType)
        <h2>{{ $contentType->name }} (v{{ $contentType->schema_version }})</h2>
        <ul aria-label="Field schema">
            @foreach ($contentType->fieldDefinitions() as $field)
                <li wire:key="field-{{ $field->name }}">{{ $field->label }} ({{ $field->type }})</li>
            @endforeach
        </ul>
    @else
        <p>Schema not found.</p>
    @endif
</div>
