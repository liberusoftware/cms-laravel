<div>
    <h2>Digital assets</h2>
    @forelse ($assets as $asset)
        <article wire:key="digital-asset-{{ $asset->id }}"><strong>{{ $asset->name }}</strong><span>{{ $asset->asset_type }}</span><span>{{ $asset->status }}</span></article>
    @empty
        <p>No digital assets are available.</p>
    @endforelse
</div>
