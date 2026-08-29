<x-filament-panels::page>
    <div class="space-y-2">
        @foreach ($this->extensions() as $extension)
            <div wire:key="extension-{{ $extension['key'] }}">{{ $extension['name'] }} ({{ $extension['version'] }}) — {{ $extension['enabled'] ? 'Enabled' : 'Disabled' }}</div>
        @endforeach
    </div>
</x-filament-panels::page>
