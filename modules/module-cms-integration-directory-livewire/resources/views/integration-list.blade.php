<div>
    @foreach ($integrations as $integration)
        <div wire:key="integration-{{ $integration->id }}">{{ $integration->name }}: {{ $integration->status }} ({{ $integration->health_status }})</div>
    @endforeach
</div>
