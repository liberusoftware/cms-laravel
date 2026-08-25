<div>
    <h2>Content integrity</h2>
    @forelse ($findings as $finding)
        <article wire:key="integrity-finding-{{ $finding->id }}"><strong>{{ $finding->kind }}</strong><span>{{ $finding->severity }}</span><p>{{ $finding->message }}</p></article>
    @empty
        <p>No integrity findings match this filter.</p>
    @endforelse
</div>
