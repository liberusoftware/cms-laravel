<div>
    <label>
        <span class="sr-only">Search processing profiles</span>
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search profiles">
    </label>
    <ul>
        @forelse ($profiles as $profile)
            <li wire:key="profile-{{ $profile->public_id }}">{{ $profile->key }} ({{ $profile->format }})</li>
        @empty
            <li>No processing profiles found.</li>
        @endforelse
    </ul>
    {{ $profiles->links() }}
</div>
