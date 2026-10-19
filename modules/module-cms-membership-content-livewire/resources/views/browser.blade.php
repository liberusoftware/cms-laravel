<div>
    <h2>Membership content</h2>
    @forelse ($items as $item)
        <article wire:key="membership-content-{{ $item->public_id }}">
            <h3>{{ $item->title }}</h3>
            <p>{{ $item->description }}</p>
        </article>
    @empty
        <p>No content is available for this membership.</p>
    @endforelse
</div>
