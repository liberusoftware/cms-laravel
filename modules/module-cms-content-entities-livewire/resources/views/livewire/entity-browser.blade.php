<div>
    <label for="cms-entity-search">Search entities</label>
    <input id="cms-entity-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($entities as $entity)
            <li wire:key="cms-entity-{{ $entity->getKey() }}">{{ $entity->title }}</li>
        @empty
            <li>No published entities found.</li>
        @endforelse
    </ul>
    {{ $entities->links() }}
</div>
