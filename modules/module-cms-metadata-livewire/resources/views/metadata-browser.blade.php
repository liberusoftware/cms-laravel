<div>
    @foreach ($metadata as $key => $value)
        <div wire:key="metadata-{{ $key }}"><strong>{{ $key }}</strong>: {{ is_scalar($value) ? $value : json_encode($value) }}</div>
    @endforeach
</div>
