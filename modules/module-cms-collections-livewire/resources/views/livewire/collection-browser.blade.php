<div>
    @if ($collectionRecord)
        <h2>{{ $collectionRecord->name }}</h2>
    @endif
    <label for="cms-collection-search">Search records</label>
    <input id="cms-collection-search" type="search" wire:model.live="search">
    <ul>
        @forelse ($records as $record)
            <li wire:key="cms-collection-record-{{ $record->getKey() }}">
                <a href="{{ url('/'.$record->slug) }}">{{ $record->title }}</a>
            </li>
        @empty
            <li>No published records found.</li>
        @endforelse
    </ul>
    {{ $records->links() }}
</div>
