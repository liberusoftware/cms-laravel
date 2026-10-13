<div>
    <label>
        <span class="sr-only">Search editorial workflows</span>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search workflows">
    </label>
    <ul>
        @forelse ($workflows as $workflow)
            <li wire:key="workflow-{{ $workflow->public_id }}">{{ $workflow->name }} ({{ $workflow->initial_state }})</li>
        @empty
            <li>No workflows found.</li>
        @endforelse
    </ul>
    {{ $workflows->links() }}
</div>
