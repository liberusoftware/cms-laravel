<div>
    @if ($release)<strong>{{ $release->key }}</strong><span>{{ $release->state }}</span><button type="button" wire:click="publish">Publish</button><button type="button" wire:click="unpublish">Unpublish</button>@else<p>Release unavailable.</p>@endif
</div>
