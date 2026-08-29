<div>
    <textarea wire:model.live="prompt" aria-label="Copilot prompt"></textarea>
    <button wire:click="submit">Submit</button>
    <p>{{ $result['status'] ?? 'Ready' }}</p>
</div>
