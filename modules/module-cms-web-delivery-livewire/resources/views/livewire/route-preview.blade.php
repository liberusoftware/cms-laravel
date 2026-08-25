<div>
    <input wire:model="path" placeholder="/path">
    <input wire:model="previewToken" placeholder="Preview token">
    <button type="button" wire:click="resolve">Preview</button>
    @if ($result)
        <p>Status: {{ $result['status'] }}</p>
        @if ($result['body']) <div>{{ $result['body'] }}</div> @endif
    @endif
</div>
