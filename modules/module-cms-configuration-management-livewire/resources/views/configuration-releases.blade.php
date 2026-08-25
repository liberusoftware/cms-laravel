<div>
    <label>Environment <input wire:model.live="environment" /></label>
    <ul>@forelse ($releases as $release)<li wire:key="release-{{ $release->id }}">{{ $release->version }} — {{ $release->status }}</li>@empty<li>No configuration releases.</li>@endforelse</ul>
</div>
