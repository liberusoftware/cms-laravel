<div>
    <label>
        <span class="sr-only">Filter suggestions by surface</span>
        <input type="search" wire:model.live.debounce.300ms="surface" placeholder="Surface">
    </label>
    <ul>
        @forelse ($suggestions as $suggestion)
            <li wire:key="suggestion-{{ $suggestion->public_id }}">{{ $suggestion->surface }} ({{ $suggestion->status }})</li>
        @empty
            <li>No experience suggestions found.</li>
        @endforelse
    </ul>
    {{ $suggestions->links() }}
</div>
