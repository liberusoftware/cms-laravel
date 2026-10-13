<div>
    <label>
        <span class="sr-only">Search persisted queries</span>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search by hash">
    </label>
    <ul>
        @forelse ($queries as $query)
            <li wire:key="query-{{ $query->id }}">{{ $query->query_hash }}</li>
        @empty
            <li>No persisted queries found.</li>
        @endforelse
    </ul>
    {{ $queries->links() }}
</div>
