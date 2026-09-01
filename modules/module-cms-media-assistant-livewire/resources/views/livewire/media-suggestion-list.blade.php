<div>
    <label>
        <span class="sr-only">Filter by asset</span>
        <input type="search" wire:model.live.debounce.300ms="assetKey" placeholder="Asset key">
    </label>
    <ul>
        @forelse ($suggestions as $suggestion)
            <li wire:key="suggestion-{{ $suggestion->public_id }}">{{ $suggestion->kind }}: {{ $suggestion->value }} ({{ $suggestion->status }})</li>
        @empty
            <li>No suggestions found.</li>
        @endforelse
    </ul>
    {{ $suggestions->links() }}
</div>
