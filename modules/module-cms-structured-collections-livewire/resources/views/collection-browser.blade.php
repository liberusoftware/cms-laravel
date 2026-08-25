<div>
    <input wire:model.live="search" placeholder="Search records">
    @if ($records)
        <ul>
            @foreach ($records as $record)
                <li>{{ $record->title }}</li>
            @endforeach
        </ul>
        {{ $records->links() }}
    @endif
</div>
