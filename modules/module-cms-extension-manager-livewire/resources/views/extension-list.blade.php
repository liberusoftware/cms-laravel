<div>
    @foreach ($extensions as $extension)
        <div wire:key="extension-{{ $extension['key'] }}">{{ $extension['name'] }}: {{ $extension['enabled'] ? 'enabled' : 'disabled' }}</div>
    @endforeach
</div>
