<div>
    <label for="cms-core-site-search">Search sites</label>
    <input id="cms-core-site-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($sites as $site)
            <li wire:key="cms-core-site-{{ $site->getKey() }}">{{ $site->name }} ({{ $site->key }})</li>
        @empty
            <li>No sites found.</li>
        @endforelse
    </ul>
    {{ $sites->links() }}
</div>
