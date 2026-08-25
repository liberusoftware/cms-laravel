<div>
    <h2>Federation source health</h2>
    @forelse ($sources as $source)
        <article wire:key="federation-source-{{ $source->id }}"><strong>{{ $source->name }}</strong><span>{{ $source->status }}</span></article>
    @empty
        <p>No federation sources are configured.</p>
    @endforelse
</div>
