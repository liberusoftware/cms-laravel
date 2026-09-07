<div>
    <label>
        <span class="sr-only">Filter forum references by provider</span>
        <input type="search" wire:model.live.debounce.300ms="provider" placeholder="Provider">
    </label>
    <ul>
        @forelse ($references as $reference)
            <li wire:key="reference-{{ $reference->public_id }}">{{ $reference->provider }} — {{ $reference->external_type }} {{ $reference->external_id }}</li>
        @empty
            <li>No forum references found.</li>
        @endforelse
    </ul>
    {{ $references->links() }}
</div>
