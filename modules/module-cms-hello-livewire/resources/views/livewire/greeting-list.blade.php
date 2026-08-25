<div>
    <label for="cms-hello-livewire-per-page">Greetings per page</label>
    <select id="cms-hello-livewire-per-page" wire:model.live="perPage">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
    </select>

    <ul aria-label="Greetings">
        @forelse ($greetings as $greeting)
            <li wire:key="greeting-{{ $greeting->getKey() }}">
                <strong>{{ $greeting->name }}</strong>
                <span>{{ $greeting->message }}</span>
            </li>
        @empty
            <li>No greetings found.</li>
        @endforelse
    </ul>

    {{ $greetings->links() }}
</div>
