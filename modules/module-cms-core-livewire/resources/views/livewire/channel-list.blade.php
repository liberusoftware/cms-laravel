<div>
    <label for="cms-core-channel-search">Search channels</label>
    <input id="cms-core-channel-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($channels as $channel)
            <li wire:key="cms-core-channel-{{ $channel->getKey() }}">{{ $channel->name }} ({{ $channel->key }})</li>
        @empty
            <li>No channels found.</li>
        @endforelse
    </ul>
    {{ $channels->links() }}
</div>
