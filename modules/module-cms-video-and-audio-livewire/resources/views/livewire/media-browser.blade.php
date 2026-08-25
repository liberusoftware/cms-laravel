<div>
    <input wire:model.live="search" placeholder="Search media">
    @forelse ($assets as $asset)
        <article wire:key="media-{{ $asset->public_id }}"><h2>{{ $asset->title }}</h2><p>{{ $asset->kind }} · {{ $asset->status }}</p><button type="button" wire:click="play('{{ $asset->public_id }}')">Play</button></article>
    @empty
        <p>No media assets.</p>
    @endforelse
    @if ($playback)<div><strong>{{ $playback['title'] }}</strong><audio controls src="{{ $playback['stream_uri'] }}"></audio></div>@endif
    {{ $assets->links() }}
</div>
