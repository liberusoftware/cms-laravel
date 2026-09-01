<div>
    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search locales">
    <ul>
        @forelse ($locales as $locale)
            <li wire:key="locale-{{ $locale->getKey() }}">{{ $locale->locale }} ({{ $locale->direction }})</li>
        @empty
            <li>No locales found.</li>
        @endforelse
    </ul>
    {{ $locales->links() }}
</div>
