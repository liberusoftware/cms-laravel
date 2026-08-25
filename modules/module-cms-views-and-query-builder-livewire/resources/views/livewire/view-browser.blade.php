<div>
    @if ($definition)
        <h2>{{ $definition->name }}</h2>
        @if ($records->count())
            <ul>
                @foreach ($records as $record)
                    <li wire:key="listing-record-{{ $record->getKey() }}">{{ $record->getAttribute('title') ?? $record->getKey() }}</li>
                @endforeach
            </ul>
            {{ $records->links() }}
        @else
            <p>No records found.</p>
        @endif
    @else
        <p>View not found.</p>
    @endif
</div>
