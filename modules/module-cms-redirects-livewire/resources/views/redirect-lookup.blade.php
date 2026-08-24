<div>
    <input type="text" wire:model.live="path" placeholder="/old-path">
    @if ($result)
        <p>Resolved path: {{ $result['path'] }}</p>
        @if ($result['loop'])<p>Redirect loop detected.</p>@endif
    @endif
</div>
