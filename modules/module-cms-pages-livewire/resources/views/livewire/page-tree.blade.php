<div>
    <label for="cms-pages-search">Search pages</label>
    <input id="cms-pages-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($pages as $page)
            @include('cms-pages-livewire::livewire.page-node', ['page' => $page])
        @empty
            <li>No pages found.</li>
        @endforelse
    </ul>
</div>
