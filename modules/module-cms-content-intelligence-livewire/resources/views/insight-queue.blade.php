<div>
    <h2>Content improvement queue</h2>
    @forelse ($insights as $insight)
        <article wire:key="content-insight-{{ $insight->id }}"><strong>{{ $insight->metric }}</strong><span>{{ $insight->severity }}</span><p>{{ $insight->summary }}</p></article>
    @empty
        <p>No open insights are available.</p>
    @endforelse
</div>
